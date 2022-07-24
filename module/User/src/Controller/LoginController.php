<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Db\Adapter\Adapter;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\Auth\LoginForm;
use User\Model\Table\UsersTable;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Session\SessionManager;

class LoginController extends AbstractActionController
{
    private $adapter;    
	private $usersTable;

    public function __construct(Adapter $adapter, UsersTable $usersTable)
	{
		$this->adapter = $adapter;
		$this->usersTable = $usersTable;
	}
    
    public function indexAction()
	{
        $auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			return $this->redirect()->toRoute('myblog');
		}

        $loginForm = new LoginForm();
        $request = $this->getRequest();

        if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$loginForm->setInputFilter($this->usersTable->getLoginFormFilter());
			$loginForm->setData($formData);

			if($loginForm->isValid()) {
				$authAdapter = new CredentialTreatmentAdapter($this->adapter);
				$authAdapter->setTableName($this->usersTable->getTable())
				            ->setIdentityColumn('email')
				            ->setCredentialColumn('password');

				# data from loginForm
				$data = $loginForm->getData();
				$returnUrl = $this->params()->fromPost('returnUrl');
				$authAdapter->setIdentity($data['email']);

                # password hashing class
				$hash = new Bcrypt();

                $info = $this->usersTable->fetchAccountByEmail($data['email']);

                # now compare password from form input with that already in the table
				if($hash->verify($data['password'], $info->getPassword())) {
					$authAdapter->setCredential($info->getPassword());
				} else {
					$authAdapter->setCredential(''); # why? to gracefully handle errors
				}
                $authResult = $auth->authenticate($authAdapter);

				switch ($authResult->getCode()) {
					case Result::FAILURE_IDENTITY_NOT_FOUND:
						$this->flashMessenger()->addErrorMessage('Unknow email address!');
						return $this->preserveUrl($returnUrl);
						//return $this->redirect()->refresh(); # refresh the page to show error
						break;

					case Result::FAILURE_CREDENTIAL_INVALID:
						$this->flashMessenger()->addErrorMessage('Incorrect Password!');
						return $this->redirect()->refresh(); # refresh the page to show error
						break;
						
					case Result::SUCCESS:
						if($data['recall'] == 1) {
							$ssm = new SessionManager();
							$ttl = 1814400; # time for session to live
							$ssm->rememberMe($ttl);
						}

						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(null, ['created', 'modified']));

						if (!empty($returnUrl)) {
							# will come back to later... if script does not work.
							return $this->preserveUrl($returnUrl);
						}

						# let us now create the profile route and we will be done
						return $this->redirect()->toRoute(
							'profile', 
							[
								'id' => $info->getUserId(),
								'username' => mb_strtolower($info->getUsername())
							]
						);

						break;		
					
					default:
						$this->flashMessenger()->addErrorMessage('Authentication failed. Try again');
						return $this->preserveUrl($returnUrl);
						//return $this->redirect()->refresh(); # refresh the page to show error
						break;
				}
			}
		}

		return (new ViewModel(['form' => $loginForm]))->setTemplate('user/auth/login');
	}

}
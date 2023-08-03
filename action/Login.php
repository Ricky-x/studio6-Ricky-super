<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;

// load base class
include_once 'Base.php';

// Login class
class Login extends \Base
{

    // Login
    public function login()
    {

        // Receive parameter email passwd
        $name = $this->REQUEST['email'] ?? '';
        $passwd = $this->REQUEST['passwd'] ?? '';

        // The parameter is empty prompt error message
        if ($name == '' || $passwd == '') {
            return sendSuccess('password account not written', [], 400);
        }

        // According to username password. Get user info from user table
        $userData = $this->DB->getOne("select * from users where name = '{$name}' and password = '{$passwd}'");
        // User does not exist Prompt error message
        if (empty($userData)) {
            return sendSuccess('password account not written', [], 400);
        }
        // User exists Return user information
        return sendSuccess('login successful', ['user_id' => $userData['id'], 'name' => $name]);
    }


    // register
    public function register()
    {
        // Receiving parameters Username Email Phone number Password
        $name = $this->REQUEST['name'] ?? '';
        $email = $this->REQUEST['email'] ?? '';
        $phone = $this->REQUEST['phone'] ?? '';
        $passwd = $this->REQUEST['passwd'] ?? '';

        // The received parameter cannot be empty, otherwise an error message will be returned
        if ($email == '' || $passwd == '' || $name == '' || $phone == '') {
            return sendSuccess('password account not written', [], 400);
        }
        // Query whether the user exists according to the username
        $userData = $this->DB->getOne("select * from users where name = '{$name}'");
        // There are data prompts This user has already registered
        if ($userData) {
            return sendSuccess('name account has been registered', [], 400);
        }
        // Query whether the user is registered according to the email address
        $userData = $this->DB->getOne("select * from users where email = '{$email}'");
        // There are data prompts This user has already registered
        if ($userData) {
            return sendSuccess('email account has been registered', [], 400);
        }

        // Insert user information into the users table
        $data = ['name' => $name, 'password' => $passwd, 'phone' => $phone, 'email' => $email];
        $this->DB->insert('users', $data);
        // Get user information, mainly get user id
        $userData = $this->DB->getOne("select * from users where name = '{$name}'");
        // Prompt that the registration is successful and return the user id and username
        return sendSuccess('registration success', ['user_id' => $userData['id'], 'name' => $name]);

    }
}


// Get the action parameter. Determine which method is called.
$action = $_REQUEST['action'] ?? '';
// Parameter does not exist Output error message
if ($action == '') {
    echo sendSuccess('Can not be empty', [], 400);
    die;
}
// Instantiate the shopping cart class
$login  = new Login();
// Call the corresponding method in the shopping cart class
echo $login->$action();

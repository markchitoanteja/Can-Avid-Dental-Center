<?php

namespace App\Controllers;

use App\Models\User_Model;
use App\Models\Message_Model;

class Auth extends BaseController
{
    private function sendWelcomeMessages(int $userId): void
    {
        $Message_Model = new Message_Model();

        $messages = [
            [
                'user_id' => $userId,
                'subject' => '🎉 Welcome to Dental Care Plus!',
                'content' => 'Thank you for joining our community! We’re excited to help you maintain a healthy smile.',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📦 Explore Our Dental Packages',
                'content' => 'Check out our affordable and comprehensive dental packages. Click here to explore: <a href="' . base_url("services") . '">View Services</a>',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📝 Complete Your Profile',
                'content' => 'To get the best experience, please complete your profile. <a href="' . base_url("client/profile") . '">Update Profile</a>',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userId,
                'subject' => '📅 Book an Appointment Anytime',
                'content' => 'Easily schedule a visit with us at your convenience. Visit the appointments section anytime!',
                'is_read' => 0,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        foreach ($messages as $msg) {
            $Message_Model->insert($msg);
        }
    }

    public function login()
    {
        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");
        $remember = $this->request->getPost("remember");

        $User_Model = new User_Model();

        $user = $User_Model
            ->where("email", $email)
            ->first();

        $success = false;

        if ($user && password_verify($password, $user["password"])) {
            $success = true;

            if ($user['user_type'] == 'admin') {
                session()->setFlashdata([
                    'title' => 'Success!',
                    'text' => "Welcome back, " . $user["name"] . "!",
                    'icon' => 'success',
                ]);
            } else {
                session()->setFlashdata([
                    'type' => 'success',
                    'message' => "Welcome back, " . $user["name"] . "!",
                ]);
            }

            session()->set("user", [
                "id" => $user["id"],
                "name" => $user["name"],
                "email" => $user["email"],
                "image" => $user["image"],
                "user_type" => $user["user_type"],
            ]);

            if ($remember) {
                session()->set("remember_me", true);
                session()->set("login_email", $email);
                session()->set("login_password", $password);
            } else {
                session()->remove("remember_me");
                session()->remove("login_email");
                session()->remove("login_password");
            }
        }

        return $this->response->setJSON([
            "success" => $success,
            "user_type" => $success ? $user["user_type"] : null,
        ]);
    }

    public function register()
    {
        $name = $this->request->getPost("name");
        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");

        $User_Model = new User_Model();

        $user = $User_Model->where("email", $email)->first();

        $success = false;
        $error_type = null;

        if ($user) {
            $error_type = "email_exists";
        } else {
            $data = [
                "uuid" => generate_uuid(),
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_BCRYPT),
                "image" => "default-user-image.webp",
                "user_type" => "user",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ];

            if ($User_Model->insert($data)) {
                $success = true;

                $newUserId = $User_Model->getInsertID();
                $this->sendWelcomeMessages($newUserId);

                // Auto-login
                $user = $User_Model->find($newUserId);
                session()->set("user", [
                    "id" => $user["id"],
                    "name" => $user["name"],
                    "email" => $user["email"],
                    "image" => $user["image"],
                    "user_type" => $user["user_type"],
                ]);

                session()->setFlashdata([
                    "type" => "success",
                    "message" => "Welcome, $name!",
                ]);
            } else {
                $error_type = "db_error";
            }
        }

        return $this->response->setJSON([
            "success" => $success,
            "error_type" => $error_type
        ]);
    }

    public function update_profile()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $User_Model = new User_Model();
        $existingUser = $User_Model->where('email', $email)->where('id !=', $id)->first();

        $success = false;
        $error_type = null;

        if ($existingUser) {
            $error_type = 'email_exists';
        } else {
            $updateData = [
                'name' => $name,
                'email' => $email,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            if ($User_Model->update($id, $updateData)) {
                $success = true;

                $updatedUser = $User_Model->find($id);
                session()->set('user', $updatedUser);

                session()->setFlashdata([
                    'type' => 'success',
                    'message' => 'Profile updated successfully!',
                ]);
            } else {
                $error_type = 'db_error';
            }
        }

        return $this->response->setJSON([
            'success' => $success,
            'error_type' => $error_type,
        ]);
    }

    public function logout()
    {
        session()->remove("user");

        session()->setFlashdata([
            "type" => "success",
            "message" => "You have been logged out successfully."
        ]);

        return $this->response->setJSON([
            "success" => true,
            "message" => "You have been logged out successfully."
        ]);
    }
}

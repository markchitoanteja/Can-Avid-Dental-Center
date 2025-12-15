<?php

namespace App\Controllers;

use App\Models\Profile_Model;
use App\Models\Package_Model;
use App\Models\Services_Model;

class Landing extends BaseController
{
    protected function getUnreadCount(): int
    {
        if (!session()->has('user')) {
            return 0;
        }

        $user = session()->get('user');

        if ($user['user_type'] !== 'user') {
            return 0;
        }

        $Message_Model = new \App\Models\Message_Model();
        return $Message_Model
            ->where('user_id', $user['id'])
            ->where('is_read', 0)
            ->countAllResults();
    }

    public function index()
    {
        session()->set('current_page', 'home');
        session()->set('page_title', 'Trusted Dental Services in Can-avid, Eastern Samar');

        $Package_Model = new Package_Model();
        $Services_Model = new Services_Model();
        $Profile_Model = new Profile_Model();

        $data['packages'] = $Package_Model->findAll();

        // Group services by category
        $services = $Services_Model->orderBy('category', 'ASC')->orderBy('name', 'ASC')->findAll();
        $groupedServices = [];
        foreach ($services as $service) {
            $groupedServices[$service['category']][] = $service['name'];
        }
        $data['groupedServices'] = $groupedServices;

        // Default phone/unread count
        $data['phone'] = '';
        $data['unreadCount'] = 0;

        if (session()->has('user')) {
            $user = session('user');

            if ($user['user_type'] === 'user') {
                $data['unreadCount'] = $this->getUnreadCount(); // Only for regular clients

                $profile = $Profile_Model->where('user_id', $user['id'])->first();
                $data['phone'] = $profile['phone'] ?? '';

                return view("landing/home", $data)
                    . view("landing/layouts/footer");
            }

            // If not a regular client, redirect to admin
            return redirect()->to(base_url('admin/dashboard'));
        }

        // Guest user: no unread count
        return view("landing/home", $data)
            . view("landing/layouts/footer");
    }

    public function home()
    {
        return redirect()->to(base_url());
    }

    public function about_us()
    {
        session()->set('current_page', 'about_us');
        session()->set('page_title', 'About Us');
        session()->set('page_description', 'Learn More About Us');

        $unreadCount = $this->getUnreadCount();
        $header = view("landing/layouts/header", ['unreadCount' => $unreadCount]);
        $body = view("landing/about_us");
        $footer = view("landing/layouts/footer");

        if (session()->has('user')) {
            $user = session()->get('user');

            if (isset($user['user_type']) && $user['user_type'] === 'user') {
                return $header . $body . $footer;
            } else {
                return redirect()->to(base_url('admin/dashboard'));
            }
        }

        return $header . $body . $footer;
    }

    public function services()
    {
        session()->set('current_page', 'services');
        session()->set('page_title', 'Our Services');
        session()->set('page_description', 'Our Services Keep You Smiling');

        $Package_Model = new \App\Models\Package_Model();
        $packages = $Package_Model->orderBy('name', 'ASC')->findAll();

        $data = [
            'packages' => $packages
        ];

        $unreadCount = $this->getUnreadCount();
        $header = view("landing/layouts/header", ['unreadCount' => $unreadCount]);
        $body   = view("landing/services", $data);
        $footer = view("landing/layouts/footer");

        if (session()->has('user')) {
            $user = session()->get('user');

            if (isset($user['user_type']) && $user['user_type'] === 'user') {
                return $header . $body . $footer;
            } else {
                return redirect()->to(base_url('admin/dashboard'));
            }
        }

        return $header . $body . $footer;
    }

    public function contact_us()
    {
        session()->set('current_page', 'contact_us');
        session()->set('page_title', 'Contact Us');
        session()->set('page_description', 'Get in Touch with Us');

        $unreadCount = $this->getUnreadCount();
        $header = view("landing/layouts/header", ['unreadCount' => $unreadCount]);
        $body = view("landing/contact_us");
        $footer = view("landing/layouts/footer");

        if (session()->has('user')) {
            $user = session()->get('user');

            if (isset($user['user_type']) && $user['user_type'] === 'user') {
                return $header . $body . $footer;
            } else {
                return redirect()->to(base_url('admin/dashboard'));
            }
        }

        return $header . $body . $footer;
    }
}

<?php

namespace App\Controllers;

use App\Models\DashboardModel;

class Lifestyle extends BaseController
{
    // Constructed //
    protected $dashboardModel;
    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }


    // Index //
    public function index()
    {
        $builder = $this->db->table('category');
        $category = $builder->get()->getResult();

        $currentPage = $this->request->getVar('page_article') ? $this->request->getVar('page_article') : 1;

        $keyword = $this->request->getVar('keyword');
        if ($keyword) {
            $search = $this->dashboardModel->search($keyword);
            session()->set('qsearch', $keyword);
            return redirect()->to('/search');
        } else {
            $search = $this->dashboardModel;
        }

        $data = [
            'title'                   => 'Lifestyle',
            'category'                => $category,
            'banner'                  => $search->orderBy('id', 'ASC')->findAll(1),

            'lifestyle'               => $search->orderBy('category', 'ASC')->where(['category' => 'lifestyle'])->findAll(1),
            'article'                 => $search->orderBy('category', 'ASC')->where(['category' => 'lifestyle'])->findAll(4, 1),
            'popular'                 => $search->orderBy('id', 'ASC')->findAll(4),
            'recent'                  => $search->orderBy('id', 'RANDOM')->findAll(4),

            'latest_post'             => $search->orderBy('id', 'ASC')->where(['category' => 'lifestyle'])->paginate(5, 'article'),
            'celebration'             => $search->orderBy('id', 'ASC')->paginate(2, 'article'),

            'pager'                   => $this->dashboardModel->pager,
            'currentPage'             => $currentPage,
        ];

        return view('/article/lifestyle', $data);
    }
    // end Index //


    // Detail //
    public function read($slug)
    {

        $builder = $this->db->table('category');
        $category = $builder->get()->getResult();

        $keyword = $this->request->getVar('keyword');
        if ($keyword) {
            $search = $this->dashboardModel->search($keyword);
            session()->set('qsearch', $keyword);
            return redirect()->to('/search');
        } else {
            $search = $this->dashboardModel;
        }

        $data = [
            'title'       => $slug,
            'article'     => $this->dashboardModel->getArticle($slug),
            'category'    => $category,

            'popular'     => $search->orderBy('id', 'ASC')->findAll(4),
            'celebration' => $search->orderBy('id', 'ASC')->paginate(2, 'article'),
        ];

        //if article not this table
        if (empty($data['article'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Article ' . $slug . ' Not Found.');
        }

        return view('/article/read', $data);
    }
    // end Detail //
}

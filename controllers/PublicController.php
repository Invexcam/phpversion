<?php

class PublicController {
    private $scanModel;
    
    public function __construct() {
        $this->scanModel = new QRScan();
    }
    
    public function home() {
        render('public/home');
    }
    
    public function getPublicStats() {
        $stats = $this->scanModel->getPublicStats();
        json_response($stats);
    }
}
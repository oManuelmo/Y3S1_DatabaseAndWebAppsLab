<?php

namespace App\Http\Controllers;


class FeaturesController extends Controller
{
    public function index()
    {
        $features = [
            'For Visitors' => [
                'Create an account to explore our auction platform.',
                'Search for specific auctions using exact keywords.',
                'Explore active auctions with easy-to-use filters.',
                'Browse auctions by category or using advanced search options.',
            ],
            'For Sellers' => [
                'Create and manage your own auctions.',
                'Edit auction details and cancel them when needed.',
                'Monitor your auctions in real-time.',
            ],
            'For Bidders' => [
                'Participate in auctions with real-time bidding.',
                'View the bidding history to analyze offers.',
                'Follow auctions to receive updates and notifications.',
                'Rate sellers based on your experience.',
            ],
            'Administrative Tools' => [
                'Manage user accounts, including editing, blocking, and deleting.',
                'Monitor and cancel auctions that violate rules.',
                'Manage auction categories and respond to auction reports.',
                'Receive notifications for reported auctions.',
            ],
        ];

        return view('pages.features', compact('features'));
    }
}

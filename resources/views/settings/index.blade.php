@extends('layouts.app')

@section('title', 'Settings Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settings Dashboard</h3>
                    <p class="text-muted mb-0">Manage your application settings and configurations</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Profile Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Profile Settings</h5>
                                    <p class="card-text">Manage your personal profile information and preferences</p>
                                    <a href="{{ route('settings.profile') }}" class="btn btn-primary">Configure</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Theme Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-purple">
                                <div class="card-body text-center">
                                    <i class="fas fa-palette fa-3x text-purple mb-3"></i>
                                    <h5 class="card-title">Theme Settings</h5>
                                    <p class="card-text">Customize colors, fonts, and visual appearance</p>
                                    <a href="{{ route('settings.theme') }}" class="btn btn-outline-purple">Configure</a>
                                </div>
                            </div>
                        </div>

                        @if(in_array(auth()->user()->role->name, ['Author', 'Admin', 'NSM+']))
                        <!-- Company Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Company Settings</h5>
                                    <p class="card-text">Configure company information and branding</p>
                                    <a href="{{ route('settings.company') }}" class="btn btn-info">Configure</a>
                                </div>
                            </div>
                        </div>

                        <!-- Application Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-cogs fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Application Settings</h5>
                                    <p class="card-text">Configure application behavior and features</p>
                                    <a href="{{ route('settings.app') }}" class="btn btn-success">Configure</a>
                                </div>
                            </div>
                        </div>

                        <!-- Email/Notifications -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Email & Notifications</h5>
                                    <p class="card-text">Configure email settings and notification preferences</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('settings.email') }}" class="btn btn-warning">Configure</a>
                                        <a href="{{ route('settings.email.test') }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-paper-plane"></i> Test Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-shield-alt fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Security Settings</h5>
                                    <p class="card-text">Manage security policies and access controls</p>
                                    <a href="{{ route('settings.security') }}" class="btn btn-danger">Configure</a>
                                </div>
                            </div>
                        </div>

                        <!-- Backup/Integrations -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-3x text-secondary mb-3"></i>
                                    <h5 class="card-title">Backup & Integrations</h5>
                                    <p class="card-text">Configure backup settings and third-party integrations</p>
                                    <a href="{{ route('settings.backup') }}" class="btn btn-secondary">Configure</a>
                                </div>
                            </div>
                        </div>

                        <!-- Updates -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-dark">
                                <div class="card-body text-center">
                                    <i class="fas fa-download fa-3x text-dark mb-3"></i>
                                    <h5 class="card-title">System Updates</h5>
                                    <p class="card-text">Manage application updates and version control</p>
                                    <a href="{{ route('settings.updates') }}" class="btn btn-dark">Configure</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Patient\PatientRepositoryInterface;
use App\Repositories\Patient\PatientRepository;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\Appointment\AppointmentRepository;
use App\Repositories\ExaminationResult\ExaminationResultRepositoryInterface;
use App\Repositories\ExaminationResult\ExaminationResultRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services and bind interfaces to implementations.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Patient repository
        $this->app->singleton(
            PatientRepositoryInterface::class,
            PatientRepository::class
        );

        // Bind Appointment repository
        $this->app->singleton(
            AppointmentRepositoryInterface::class,
            AppointmentRepository::class
        );


        // Bind ExaminationResult repository
        $this->app->singleton(
            ExaminationResultRepositoryInterface::class,
            ExaminationResultRepository::class
        );


        // Bind User repository (using bind instead of singleton)
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {

        // Add boot logic if needed

    }
}

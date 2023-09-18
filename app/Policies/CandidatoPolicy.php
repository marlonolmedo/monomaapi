<?php

namespace App\Policies;

use App\Models\Candidato;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CandidatoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // public function viewAny(User $user)
    // {
    //     //
    // }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Candidato $candidato)
    {
        return $user->role == 'agent' ? $user->id == $candidato->owner : true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role == 'manager';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // public function update(User $user, Candidato $candidato)
    // {
    //     //
    // }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // public function delete(User $user, Candidato $candidato)
    // {
    //     //
    // }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // public function restore(User $user, Candidato $candidato)
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Candidato  $candidato
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // public function forceDelete(User $user, Candidato $candidato)
    // {
    //     //
    // }
}

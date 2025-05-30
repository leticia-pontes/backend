<?php

namespace App\Policies;

use App\Models\Avaliacao;
use App\Models\Empresa;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvaliacaoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Empresa $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Empresa $user, Avaliacao $avaliacao): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Empresa $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Empresa $user, Avaliacao $avaliacao): bool
    {
        // Só permite atualizar se for a empresa que fez a avaliação
        return $user->id_empresa === $avaliacao->id_empresa_avaliadora;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Empresa $user, Avaliacao $avaliacao): bool
    {
        // Mesma regra para deletar
        return $user->id_empresa === $avaliacao->id_empresa_avaliadora;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Empresa $user, Avaliacao $avaliacao): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Empresa $user, Avaliacao $avaliacao): bool
    {
        return false;
    }
}

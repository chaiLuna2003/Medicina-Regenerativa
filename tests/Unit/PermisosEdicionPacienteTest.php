<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\Pacientes\PermisosEdicionPaciente;
use PHPUnit\Framework\TestCase;

class PermisosEdicionPacienteTest extends TestCase
{
    public function test_administracion_y_recepcion_comparten_todos_los_campos(): void
    {
        $permisos = new PermisosEdicionPaciente;

        $camposAdministrador = $permisos->camposPara(
            $this->usuario('admin')
        );

        $camposRecepcion = $permisos->camposPara(
            $this->usuario('recepcionista')
        );

        $this->assertSame(
            $camposAdministrador,
            $camposRecepcion
        );

        $this->assertContains(
            'telefono',
            $camposRecepcion
        );

        $this->assertContains(
            'telefono_fijo',
            $camposRecepcion
        );

        $this->assertContains(
            'telefono_secundario',
            $camposRecepcion
        );
    }

    public function test_medico_edita_datos_generales_pero_no_telefonos(): void
    {
        $permisos = new PermisosEdicionPaciente;
        $medico = $this->usuario('medico');

        $this->assertTrue(
            $permisos->puedeEditar(
                $medico,
                'nombre'
            )
        );

        $this->assertTrue(
            $permisos->puedeEditar(
                $medico,
                'fecha_nacimiento'
            )
        );

        $this->assertTrue(
            $permisos->puedeEditar(
                $medico,
                'email'
            )
        );

        $this->assertTrue(
            $permisos->puedeEditar(
                $medico,
                'alergias'
            )
        );

        $this->assertTrue(
            $permisos->puedeEditar(
                $medico,
                'notas'
            )
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $medico,
                'telefono'
            )
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $medico,
                'telefono_fijo'
            )
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $medico,
                'telefono_secundario'
            )
        );
    }

    public function test_enfermeria_no_recibe_campos_implicitamente(): void
    {
        $permisos = new PermisosEdicionPaciente;
        $enfermero = $this->usuario('enfermero');

        $this->assertSame(
            [],
            $permisos->camposPara($enfermero)
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $enfermero,
                'sexo'
            )
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $enfermero,
                'alergias'
            )
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $enfermero,
                'telefono'
            )
        );
    }

    public function test_rol_desconocido_no_recibe_permisos(): void
    {
        $permisos = new PermisosEdicionPaciente;
        $usuario = $this->usuario(
            'rol_inexistente'
        );

        $this->assertSame(
            [],
            $permisos->camposPara($usuario)
        );

        $this->assertFalse(
            $permisos->puedeEditar(
                $usuario,
                'nombre'
            )
        );
    }

    private function usuario(string $rol): User
    {
        return new User([
            'role' => $rol,
        ]);
    }
}

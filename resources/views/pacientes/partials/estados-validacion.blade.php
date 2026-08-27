 <div
        id="estado-validacion-historia"
        data-tiene-errores="{{ $errors->hasAny([
        'patologia_base',
        'padecimiento_actual',
        'tratamientos_actuales',
        'prioridad_analisis_medico',
    ]) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-heredofamiliares"
        data-tiene-errores="{{ (
        $errors->heredofamiliares->has('numero_hermanos')
        || $errors->heredofamiliares->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-habitos-alimenticios"
        data-tiene-errores="{{ (
        $errors
            ->habitosAlimenticios
            ->has('comidas.*')
        || $errors
            ->habitosAlimenticios
            ->has('alimentos.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-personales-patologicos"
        data-tiene-errores="{{ (
        $errors
            ->personalesPatologicos
            ->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-personales-no-patologicos"
        data-tiene-errores="{{ (
        $errors
            ->personalesNoPatologicos
            ->has('antecedentes.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>

    <div
        id="estado-validacion-exploracion-fisica"
        data-tiene-errores="{{ (
        $errors->exploracionFisica->hasAny([
            'interrogatorio',
            'anotaciones',
            'recomendaciones',
        ])
        || $errors
            ->exploracionFisica
            ->has('sistemas.*')
    ) ? 'true' : 'false' }}"
        hidden>
    </div>
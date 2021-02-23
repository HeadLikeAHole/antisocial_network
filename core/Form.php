<?php


namespace core;


class Form
{
    public Model $model;
    public array $options;

    public function __construct(Model $model, $options = [])
    {
        $this->model = $model;
        $this->options = $options;
    }

    public function render()
    {
        foreach ($this->model->formInputs() as $inputName) {
            if (isset($this->options['exclude']) && in_array($inputName, $this->options['exclude'])) {
                continue;
            }
            // call method specified in "options" array (input is default)
            call_user_func([Form::class, $this->options['type'] ?? 'input'], $inputName);
        }
    }

    public function input($inputName)
    {
        echo sprintf('<label for="%s">%s</label>', $inputName, $this->convertToLabel($inputName),);

        echo sprintf('<input type="%s" id="%s" name="%s" value="%s" placeholder="%s">',
            // check if "options" array contains "inputTypes" key and if it does check for input name there
            (isset($this->options['inputTypes']) ? (isset($this->options['inputTypes'][$inputName]) ? $this->options['inputTypes'][$inputName] : 'text') : 'text'),
            $inputName,
            $inputName,
            $this->model->$inputName ?? '',
            $this->options['placeholder'] ?? '',
        );

        echo '<ul class="input-errors">';
        foreach ($this->model->getErrors($inputName) as $error) {
            echo sprintf('<li class="input-invalid">%s</li>', $error);
        }
        echo '</ul>';
    }

    public function convertToLabel($inputName)
    {
        return ucwords(str_replace('_', ' ', $inputName)) . ': ';
    }
}
<?php

/**
 * yform.
 *
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */
class rex_yform_validate_empty extends rex_yform_validate_abstract
{
    public function enterObject()
    {
        $names = $this->getElement('name');
        if ('' == $names) {
            return;
        }

        $names = explode(',', $names);

        $warningObjects = [];
        $Value = $this->getValueObject();
        $label = $Value->getElement('label');
        foreach ($this->getObjects() as $Object) {
            if ($this->isObject($Object) && in_array($Object->getName(), $names)) {
                if ('' != $Object->getValue()) {
                    return;
                }
                $warningObjects[] = $Object;
            }
        }

        foreach ($warningObjects as $warningObject) {
            $msg = Wildcard::parse($this->getElement('message'));
            $this->params['warning'][$warningObject->getId()] = $this->params['error_class'];
            $this->params['warning_messages'][$warningObject->getId()] = str_replace('{{fieldname}}', $label, $msg);
        }
    }

    public function getDescription()
    {
        return 'validate|empty|name_1,name2,name3|warning_message ';
    }

    public function getDefinitions($values = [])
    {
        return [
            'type' => 'validate',
            'name' => 'empty',
            'values' => [
                'name' => ['type' => 'select_names', 'multiple' => true, 'label' => rex_i18n::msg('yform_validate_empty_name'), 'notice' => rex_i18n::msg('yform_validate_empty_notices_name')],
                'message' => ['type' => 'text',        'label' => rex_i18n::msg('yform_validate_empty_message')],
            ],
            'description' => rex_i18n::msg('yform_validate_empty_description'),
            'famous'      => true,
        ];
    }
}

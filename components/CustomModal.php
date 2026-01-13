<?php

namespace app\components;

use yii\bootstrap5\Modal as BootstrapModal;

class CustomModal extends BootstrapModal
{
    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        $this->headerOptions = false;
    }

    /**
     * Renders the header HTML markup of the modal.
     * @return string the rendering result
     */
    protected function renderHeader(): string
    {
        // Render header only if headerOptions is not false
        if ($this->headerOptions === false) {
            return '';
        }

        return parent::renderHeader();
    }
}

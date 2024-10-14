<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SaasLoginRecord $saasLoginRecord
 * @var \Cake\Collection\CollectionInterface|string[] $saasAdmins
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Saas Login Records'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="saasLoginRecords form content">
            <?= $this->Form->create($saasLoginRecord) ?>
            <fieldset>
                <legend><?= __('Add Saas Login Record') ?></legend>
                <?php
                    echo $this->Form->control('saas_admin_id', ['options' => $saasAdmins]);
                    echo $this->Form->control('ip');
                    echo $this->Form->control('success');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

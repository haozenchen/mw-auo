<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SaasLoginRecord $saasLoginRecord
 * @var string[]|\Cake\Collection\CollectionInterface $saasAdmins
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $saasLoginRecord->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $saasLoginRecord->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Saas Login Records'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="saasLoginRecords form content">
            <?= $this->Form->create($saasLoginRecord) ?>
            <fieldset>
                <legend><?= __('Edit Saas Login Record') ?></legend>
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

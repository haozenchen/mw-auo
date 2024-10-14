<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SaasLoginRecord $saasLoginRecord
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Saas Login Record'), ['action' => 'edit', $saasLoginRecord->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Saas Login Record'), ['action' => 'delete', $saasLoginRecord->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saasLoginRecord->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Saas Login Records'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Saas Login Record'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="saasLoginRecords view content">
            <h3><?= h($saasLoginRecord->success) ?></h3>
            <table>
                <tr>
                    <th><?= __('Saas Admin') ?></th>
                    <td><?= $saasLoginRecord->has('saas_admin') ? $this->Html->link($saasLoginRecord->saas_admin->name, ['controller' => 'SaasAdmins', 'action' => 'view', $saasLoginRecord->saas_admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Ip') ?></th>
                    <td><?= h($saasLoginRecord->ip) ?></td>
                </tr>
                <tr>
                    <th><?= __('Success') ?></th>
                    <td><?= h($saasLoginRecord->success) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($saasLoginRecord->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($saasLoginRecord->created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

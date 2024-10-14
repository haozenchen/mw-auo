<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\SaasLoginRecord> $saasLoginRecords
 */
?>
<div class="saasLoginRecords index content">
    <?= $this->Html->link(__('New Saas Login Record'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Saas Login Records') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('saas_admin_id') ?></th>
                    <th><?= $this->Paginator->sort('ip') ?></th>
                    <th><?= $this->Paginator->sort('success') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saasLoginRecords as $saasLoginRecord): ?>
                <tr>
                    <td><?= $this->Number->format($saasLoginRecord->id) ?></td>
                    <td><?= $saasLoginRecord->has('saas_admin') ? $this->Html->link($saasLoginRecord->saas_admin->name, ['controller' => 'SaasAdmins', 'action' => 'view', $saasLoginRecord->saas_admin->id]) : '' ?></td>
                    <td><?= h($saasLoginRecord->ip) ?></td>
                    <td><?= h($saasLoginRecord->success) ?></td>
                    <td><?= h($saasLoginRecord->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $saasLoginRecord->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $saasLoginRecord->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $saasLoginRecord->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saasLoginRecord->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>

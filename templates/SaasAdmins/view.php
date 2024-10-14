<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SaasAdmin $saasAdmin
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Saas Admin'), ['action' => 'edit', $saasAdmin->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Saas Admin'), ['action' => 'delete', $saasAdmin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saasAdmin->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Saas Admins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Saas Admin'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="saasAdmins view content">
            <h3><?= h($saasAdmin->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Username') ?></th>
                    <td><?= h($saasAdmin->username) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($saasAdmin->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Visit From') ?></th>
                    <td><?= h($saasAdmin->last_visit_from) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mfa Key') ?></th>
                    <td><?= h($saasAdmin->mfa_key) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($saasAdmin->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Visit') ?></th>
                    <td><?= h($saasAdmin->last_visit) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($saasAdmin->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($saasAdmin->modified) ?></td>
                </tr>
                <tr>
                    <th><?= __('Active') ?></th>
                    <td><?= $saasAdmin->active ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Km Edit') ?></th>
                    <td><?= $saasAdmin->is_km_edit ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Km Del') ?></th>
                    <td><?= $saasAdmin->is_km_del ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Km Eff') ?></th>
                    <td><?= $saasAdmin->is_km_eff ? __('Yes') : __('No'); ?></td>
                </tr>
                <tr>
                    <th><?= __('Is Mfa') ?></th>
                    <td><?= $saasAdmin->is_mfa ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Dashboard Setting') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($saasAdmin->dashboard_setting)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Sys Auth') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($saasAdmin->sys_auth)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Mfa Backup Codes') ?></h4>
                <?php if (!empty($saasAdmin->mfa_backup_codes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Saas Admin Id') ?></th>
                            <th><?= __('Passwd') ?></th>
                            <th><?= __('Used') ?></th>
                            <th><?= __('Creator') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($saasAdmin->mfa_backup_codes as $mfaBackupCodes) : ?>
                        <tr>
                            <td><?= h($mfaBackupCodes->id) ?></td>
                            <td><?= h($mfaBackupCodes->saas_admin_id) ?></td>
                            <td><?= h($mfaBackupCodes->passwd) ?></td>
                            <td><?= h($mfaBackupCodes->used) ?></td>
                            <td><?= h($mfaBackupCodes->creator) ?></td>
                            <td><?= h($mfaBackupCodes->created) ?></td>
                            <td><?= h($mfaBackupCodes->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'MfaBackupCodes', 'action' => 'view', $mfaBackupCodes->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'MfaBackupCodes', 'action' => 'edit', $mfaBackupCodes->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'MfaBackupCodes', 'action' => 'delete', $mfaBackupCodes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $mfaBackupCodes->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Saas Admin Auth Groups') ?></h4>
                <?php if (!empty($saasAdmin->saas_admin_auth_groups)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Saas Admin Id') ?></th>
                            <th><?= __('Saas Auth Group Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($saasAdmin->saas_admin_auth_groups as $saasAdminAuthGroups) : ?>
                        <tr>
                            <td><?= h($saasAdminAuthGroups->id) ?></td>
                            <td><?= h($saasAdminAuthGroups->saas_admin_id) ?></td>
                            <td><?= h($saasAdminAuthGroups->saas_auth_group_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'SaasAdminAuthGroups', 'action' => 'view', $saasAdminAuthGroups->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'SaasAdminAuthGroups', 'action' => 'edit', $saasAdminAuthGroups->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'SaasAdminAuthGroups', 'action' => 'delete', $saasAdminAuthGroups->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saasAdminAuthGroups->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Saas Login Records') ?></h4>
                <?php if (!empty($saasAdmin->saas_login_records)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Saas Admin Id') ?></th>
                            <th><?= __('Ip') ?></th>
                            <th><?= __('Success') ?></th>
                            <th><?= __('Created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($saasAdmin->saas_login_records as $saasLoginRecords) : ?>
                        <tr>
                            <td><?= h($saasLoginRecords->id) ?></td>
                            <td><?= h($saasLoginRecords->saas_admin_id) ?></td>
                            <td><?= h($saasLoginRecords->ip) ?></td>
                            <td><?= h($saasLoginRecords->success) ?></td>
                            <td><?= h($saasLoginRecords->created) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'SaasLoginRecords', 'action' => 'view', $saasLoginRecords->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'SaasLoginRecords', 'action' => 'edit', $saasLoginRecords->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'SaasLoginRecords', 'action' => 'delete', $saasLoginRecords->id], ['confirm' => __('Are you sure you want to delete # {0}?', $saasLoginRecords->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Sync Records') ?></h4>
                <?php if (!empty($saasAdmin->sync_records)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Saas Admin Id') ?></th>
                            <th><?= __('Ip Address Ip') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('User Total') ?></th>
                            <th><?= __('User Update') ?></th>
                            <th><?= __('User Threshold') ?></th>
                            <th><?= __('Department Total') ?></th>
                            <th><?= __('Department Update') ?></th>
                            <th><?= __('Department Threshold') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($saasAdmin->sync_records as $syncRecords) : ?>
                        <tr>
                            <td><?= h($syncRecords->id) ?></td>
                            <td><?= h($syncRecords->saas_admin_id) ?></td>
                            <td><?= h($syncRecords->ip_address_ip) ?></td>
                            <td><?= h($syncRecords->status) ?></td>
                            <td><?= h($syncRecords->user_total) ?></td>
                            <td><?= h($syncRecords->user_update) ?></td>
                            <td><?= h($syncRecords->user_threshold) ?></td>
                            <td><?= h($syncRecords->department_total) ?></td>
                            <td><?= h($syncRecords->department_update) ?></td>
                            <td><?= h($syncRecords->department_threshold) ?></td>
                            <td><?= h($syncRecords->created) ?></td>
                            <td><?= h($syncRecords->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'SyncRecords', 'action' => 'view', $syncRecords->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'SyncRecords', 'action' => 'edit', $syncRecords->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'SyncRecords', 'action' => 'delete', $syncRecords->id], ['confirm' => __('Are you sure you want to delete # {0}?', $syncRecords->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php

/*
 * Summary of Code Fails
 */

defined('C5_EXECUTE') || die(_("Access Denied."));
?>

<table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th><?php echo t('User') ?></a></th>
            <th><?php echo t('Email') ?></th>
            <th><?php echo t('Creation Date') ?></th>
            <th><?php echo t('Info') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        if (isset($codeFailList) && !empty($codeFailList)) {
            foreach ($codeFailList as $codeFail) {
                $accessCode = trim(end(split(':', $codeFail->Info)));
    ?>
            <tr class="ccm-list-record" >
            <td>
                <a href="<?php echo $this->url("/dashboard/users", "search?uID=$codeFail->uID") ?>">
                    <?php echo $codeFail->uID . " - " . $codeFail->uName ?>
                </a>
            </td>
            <td><?php echo $codeFail->uEmail; ?></td>
            <td><?php echo $codeFail->CreatedDate; ?></td>
            <td>
                <a href="<?php  echo $this->url("/dashboard/code_check", $accessCode) ?>">
                    <?php echo $codeFail->Info; ?>
                </a>
            </td>
            </tr>
    <?php
            }
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">
                <?php  //if (isset($titlesLists)) { echo $titlesListsPagination; } else { echo $titlesPagination; } ?>
            </td>
        </tr>
        <tr></tr>
    </tfoot>
</table>

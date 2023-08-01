<div id="ccm-tab-content-tab-1" class="ccm-tab-content">
    <div class="panel">
        <table width="100%" cellpadding="5" cellspacing="5" border="1">
            <tr>
                <td align="left">
                    <form id="add-subscription"
                          action="<?php echo $this->action('add') ?>">
                        <input type="text" name="subscription" id="gigya-search-subscriptions"
                               placeholder="Search Product"
                               search-url="<?php echo $url ?>"/>
                        <input type="hidden" name="sa_id" id="sa_id"/>
                        <input type="hidden" name="s_id" id="s_id"/>
                        <input type="hidden" name="product_id" id="product_id"/>
                        <input type="submit" class="btn primary" id="gigya-add-subscription"
                               value="<?php echo t('Add') ?>"/>
                    </form>
                </td>
            </tr>
        </table>
        <div class="alert" id="subscription-alert" role="alert"
             style="display:none;"></div>
    </div>
    <div style="clear:both"></div>
    <div class="panel-default">
        <!-- GCAP-839 Added by mtanada 20200421 reference SB-295,303 -->
        <p class="header">
            <strong>User Subscriptions</strong>
            <span>
                <input type="submit" class="btn primary submit" id="endDate" value="End Date" disabled="disabled"/>
            </span>
            <span>
                <input type="submit" class="btn primary submit" id="archiveSub" value="Archive" disabled="disabled"/>
            </span>
            <span>
                <input type="submit" class="btn primary submit" id="deactivate" value="Deactivate" disabled="disabled"/>
            </span>
            <span>
                <input
                    type="submit"
                    class="btn primary submit activate"
                    id="activate"
                    value="Activate"
                    disabled="disabled"/>
            </span>
        </p>
        <div class="usersubscription" id="subs">

        </div>
    </div>
</div>
<div id="dialog-confirm" title="">
    <p id="confirmText"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></p>
</div>
<div id="dialog-endDate">
    <p id="confirmEndDateText">
        <span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
    </p>
    <p id="endDateText">
        Updated End Date: <input type="text" id="datepicker">
    </p>
</div>
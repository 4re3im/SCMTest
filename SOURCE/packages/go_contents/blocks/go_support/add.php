<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>
<script>
    tinyMCE.init({
    mode : "textareas",
    width: "100%", 
    height: "250px",    
    inlinepopups_skin : "concreteMCE",
    theme_concrete_buttons2_add : "spellchecker",
    relative_urls : false,
    document_base_url: "<?php echo BASE_URL . DIR_REL?>/",
    convert_urls: false,
    plugins: "paste,inlinepopups,spellchecker,safari",
    theme : "advanced",
    theme_advanced_toolbar_location  : "top",
    editor_selector : "ccm-advanced-editor",
    theme_advanced_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
    theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
    theme_advanced_toolbar_align : "left",
    spellchecker_languages : "+English=en"

    //,styleselect,fontselect,fontsizeselect,strikethrough,justifyfull,justifyleft,justifycenter,justifyright,|
    });
 </script>

<div class="ccm-ui">
    <div class="alert-message block-message info">
        <?php echo t("This is the add template for the support block.") ?>
    </div>

    <?php echo $form->label('title', t('Title')) ?>
    <?php echo $form->text('title') ?>

    <?php echo $form->label('content', t('Content')) ?>
    <?php echo $form->textarea('content', $content, array('class'=>"ccm-advanced-editor")) ?>
</div>
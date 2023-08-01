<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
$form = Loader::helper('form');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls');

$pkgHandle = 'institution_management';

$elements = [
    'name' => [
        'label' => 'School Name',
        'required' => true,
        'type' => 'text'

    ],
    'addressLine1' => [
        'label' => 'Address Line 1',
        'required' => true,
        'type' => 'text'
    ],
    'addressLine2' => [
        'label' => 'Address Line 2',
        'required' => false,
        'type' => 'text'
    ],
    'addressCity' => [
        'label' => 'City/Town',
        'required' => true,
        'type' => 'text'
    ],
    'addressRegion' => [
        'label' => 'County/State',
        'required' => true,
        'type' => 'text'
    ],
    'addressCountry' => [
        'label' => 'Country',
        'required' => true,
        'type' => 'text'
    ],
    'addressCountryCode' => [
        'label' => 'Country code',
        'required' => false,
        'type' => 'text'
    ],
    'addressRegionCode' => [
        'label' => 'Post/Zipcode',
        'required' => true,
        'type' => 'text'
    ],
    'telephone' => [
        'label' => 'Phone number',
        'required' => true,
        'type' => 'text'
    ],
    'url' => [
        'label' => 'Website',
        'required' => true,
        'type' => 'text'
    ],
    'edueltTeacherCode' => [
        'label' => 'EDUELT Teacher Code',
        'required' => false,
        'type' => 'text'
    ],
    'oid' => [
        'label' => 'OID',
        'type' => 'hidden'
    ],
    'isVerified' => [
        'label' => 'isVerified',
        'type' => 'hidden'
    ]
];

?>
<div class="span12">
    <?php foreach ($elements as $key => $value) {
        $name = "institution[$key]";
        $formValue = @$entry[$key];
        ?>
        <?php if ($value['type'] !== 'hidden') { ?>
            <div class="clearfix">
                <?php
                $text = $value['required'] ? t($value['label']) . '<span class="ccm-required">*</span>' : t($value['label']);

                if ($value['type'] !== 'hidden') {
                    echo $form->label($value['label'], $text);
                }
                ?>
                <div class="input">
                    <?php
                    $attributes = [
                        'class' => 'span8',
                    ];

                    if ($value['required']) {
                        $attributes['required'] = true;
                    }

                    echo $form->$value['type']($name, $formValue, $attributes);
                    ?>
                </div>
            </div>
        <?php } else {
            echo $form->$value['type']($name, $formValue);
        } ?>
    <?php } ?>
</div>

<div class="clearfix"></div>
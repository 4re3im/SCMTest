<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupLandingPageSubjectsBlockController extends BlockController
{
	public function getBlockTypeDescription()
    {
		return t("CUP Landing Page Subjects");
	}

	public function getBlockTypeName()
    {
		return t("CUP Landing Page Subjects");
	}

	protected $btTable = 'btCupLandingPageSubjects';
	protected $btInterfaceWidth = "800";
	protected $btInterfaceHeight = "600";

	public function __construct($obj = null)
    {
		parent::__construct($obj);
	}

	public function add()
    {
		$bt = BlockType::getByHandle($this->btHandle);
		$uh = Loader::helper('concrete/urls');
		$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="'. $uh->getBlockTypeAssetsURL($bt, 'auto.css') .'" />');

		$this->set('config', array());
	}

	public function edit()
    {
		$bt = BlockType::getByHandle($this->btHandle);
		$uh = Loader::helper('concrete/urls');
		$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="'. $uh->getBlockTypeAssetsURL($bt, 'auto.css') .'" />');

		$this->set('config', @json_decode($this->config, true));
	}

	public function view()
    {
		$this->set('config', @json_decode($this->config, true));
	}

	public function save($args)
    {
		$args['config'] = json_encode($args['config']);
		parent::save($args);
	}
}

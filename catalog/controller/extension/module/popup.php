<?php
class ControllerExtensionModulePopup extends Controller {

	public function index($setting = null) {
		$this->load->language('extension/module/popup');

		if ($setting && $setting['status']) {
				$show_modal = false;
				$data = array();

				if (!isset($this->session->data['module_popup_pass'])) {
					$show_modal = true;
				}

				if ($show_modal) {
					$data['message'] = $setting['message'];

					return $this->load->view('extension/module/popup', $data);
				}
		}
	}
}

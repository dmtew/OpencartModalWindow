<?php
class ControllerExtensionModulePopup extends Controller {
	private $error = array();

	const DEFAULT_MODULE_SETTINGS = [
		'name' => 'my_test_popup',
		'message' => 'https://okeygeek.ru/wp-content/uploads/2020/08/telegram-2048x2048.png',
		'status' => 1 /* Enabled by default*/
	];

	public function index() {
		if (isset($this->request->get['module_id'])) {
			$this->configure($this->request->get['module_id']);
		} else {
			$this->load->model('setting/module');
			$this->model_setting_module->addModule('popup', self::DEFAULT_MODULE_SETTINGS); /* Becouse modules are being deleted by extension name */
			$this->response->redirect($this->url->link('extension/module/popup','&user_token='.$this->session->data['user_token'].'&module_id='.$this->db->getLastId()));
		}
	}

	protected function configure($module_id) {
		$this->load->model('setting/module');
		$this->load->language('extension/module/popup');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/popup', 'user_token=' . $this->session->data['user_token'], true)
		);

		$module_setting = $this->model_setting_module->getModule($module_id);

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} else {
			$data['name'] = $module_setting['name'];
		}

		if (isset($this->request->post['message'])) {
			$data['message'] = $this->request->post['message'];
		} else {
			$data['message'] = $module_setting['message'];
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else {
			$data['status'] = $module_setting['status'];
		}

		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=module');
		$data['action']['save'] = "";

		$data['error'] = $this->error;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/popup', $data));
	}

	public function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/popup')) {
			$this->error['permission'] = true;
			return false;
		}

		if (!utf8_strlen($this->request->post['name'])) {
			$this->error['name'] = true;
		}

		if (!utf8_strlen($this->request->post['message'])) {
			$this->error['message'] = true;
		}

		return empty($this->error);
	}

	public function install() {
		$this->load->model('setting/setting');
		$this->load->model('setting/module');

		$this->model_setting_setting->editSetting('module_popup', ['module_popup_status'=>1]);
		$this->model_setting_module->addModule('popup', self::DEFAULT_MODULE_SETTINGS);
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_popup');
	}
}

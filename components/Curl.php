<?php

namespace app\components;

class Curl {

	public $httpCode;
    public $error;
    public $result;

	protected $ch;
	protected $options = array(
		CURLOPT_HEADER           => false,
		CURLOPT_FOLLOWLOCATION   => 1,
		CURLOPT_RETURNTRANSFER   => true,
		CURLOPT_CONNECTTIMEOUT   => 3,
		CURLOPT_USERAGENT        => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5',
	);
    protected $url;
    protected $query=array();

	/**
	 * Creates and returns a new curl.
	 * @return Curl
	 */
	static public function factory() {
		return new Curl();
	}

	public function __construct() {
		$this->ch = curl_init();
	}

	public function url($url) {
		$this->url = $url;
		return $this;
	}

    public function getUrl() {
        return $this->url."?".http_build_query($this->query);
    }

	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Файл, в который будет записан результат передачи.
	 * @param string $path
     * @return Curl
	 */
	public function download($path) {
		$file = fopen( $this->directory($path), "w" );
		$this->options[CURLOPT_FILE] = $file;
		return $this;
	}

	/**
	 * Файл, из которого должно идти чтение данных, при загрузке на сервер.
	 * @param string $path
     * @return Curl
	 */
	public function upload($path) {
		$file = fopen($path, "r" );
		$this->options[CURLOPT_INFILE] = $file;
		return $this;
	}

	public function auth($login=NULL, $pass=NULL) {
		if (empty($login) || empty($pass)) {
			return $this;
		}

		// CURLAUTH_ANY - это псевдоним CURLAUTH_BASIC | CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM
		$this->options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		$this->options[CURLOPT_USERPWD] = "{$login}:{$pass}";
		return $this;
	}

	public function options($options) {
		$this->options = array_merge($this->options, $options);
		return $this;
	}

	public function bind_progress($callback) {
		$this->options[CURLOPT_NOPROGRESS] = false;
		// Big buffer less progress info/callbacks
		// Small buffer more progress info/callbacks
		$this->options[CURLOPT_BUFFERSIZE] = 1024;
		$this->options[CURLOPT_PROGRESSFUNCTION] = $callback;
		return $this;
	}

	public function execute() {
        $this->options[CURLOPT_URL] = $this->getUrl();
		curl_setopt_array($this->ch, $this->options);
		$this->result = curl_exec($this->ch);
        $this->httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $this->error = curl_error($this->ch);

		curl_close($this->ch);

		return ($this->result && ($this->httpCode == 200));
	}

	protected function directory($file) {
		$dir = dirname($file);
		if (! is_dir($dir)) {
			if (! mkdir($dir, 0755, true) ) {
				throw new Exception("Ошибка создания директории {$dir}");
			}
		}
		return $file;
	}

	public function cookie($file=NULL) {
		$file = empty($file) ? __FILE__."/cookie.txt" : $file;

		// параметр передаёт поля, содержащие полные данные, которые передаются методом POST.
		// Можно для удобство записать эти данные в переменную.
		// Именно здесь Вы передаёте поля, полученные Вами от сниффера – имя пользователя, пароль и так далее.
		//$this->options[CURLOPT_POSTFIELDS] = $this->directory($file);
		// параметр нужен для того, чтобы записывать куки (cookies), получаемые от сервера, с тем,
		// чтобы потом передать эти куки серверу при авторизации
		$this->options[CURLOPT_COOKIEJAR] = $this->directory($file);
		return $this;
	}

    public function cookieFile($file)
    {
        $this->options[CURLOPT_COOKIEFILE] = $file;
        return $this;
    }

    public function cookieVar($cookie){
        $this->options[CURLOPT_COOKIE] = $cookie;
        return $this;
    }

	public function ignoreSsl()
	{
		$this->options[CURLOPT_SSL_VERIFYHOST] = false;
		$this->options[CURLOPT_SSL_VERIFYPEER] = false;
		return $this;
	}

	/**
	 *
	 * @param array $data array('name' => 'Foo', 'file' => '@/home/user/test.png')
	 * @param string $data "name=Foo&file=@/home/user/test.png"
	 */
	public function post($data) {
		$this->options[CURLOPT_POST] = true;
		$this->options[CURLOPT_POSTFIELDS] = $data;
		return $this;
	}

    public function query($data) {
        if (is_array($data)) {
            $this->query = array_merge($this->query, $data);
        } else {
            parse_str($data, $this->query);
        }
        return $this;
    }

    public function get($data) {
        return $this->query($data);
    }

}

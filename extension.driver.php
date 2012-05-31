<?php

	if( !defined('__IN_SYMPHONY__') ) die('<h2>Error</h2><p>You cannot directly access this file</p>');
	
	
	
	require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLang.php');



	Class extension_flang_detection_gtlds extends Extension
	{


		/*------------------------------------------------------------------------------------------------*/
		/*  Installation  */
		/*------------------------------------------------------------------------------------------------*/

		public function install(){
			return $this->__updateRewriteRules('create') && $this->__updateRewriteRules('edit', FLang::getLangs());
		}

		public function enable(){
			return $this->__updateRewriteRules('create') && $this->__updateRewriteRules('edit', FLang::getLangs());
		}

		public function disable(){
			return $this->__updateRewriteRules('edit');
		}

		public function uninstall(){
			return $this->__updateRewriteRules('remove');
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Delegates  */
		/*------------------------------------------------------------------------------------------------*/

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/extensions/frontend_localisation/',
					'delegate' => 'FLSavePreferences',
					'callback' => 'dFLSavePreferences'
				),
			);
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  .htacces management  */
		/*------------------------------------------------------------------------------------------------*/

		public function dFLSavePreferences($context){
			if( false === $this->__updateRewriteRules('edit', $context['new_langs']) ){
				$context['errors']['flang_detection_gtlds'] = __('There were errors writing the <code>.htaccess</code> file. Please verify it is writable.');
				return false;
			}

			return true;
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Utilities  */
		/*------------------------------------------------------------------------------------------------*/

		private function __updateRewriteRules($mode, $langs = array()){
			$htaccess = @file_get_contents(DOCROOT.'/.htaccess');

			if( $htaccess === false ) return false;

			switch( $mode ){
				case 'create':
					$htaccess = $this->__createLanguageRules($htaccess);
					break;
				case 'edit':
					$htaccess = $this->__editLanguageRules($htaccess, $langs);
					break;
				case 'remove':
					$htaccess = $this->__removeLanguageRules($htaccess);
					break;
			}

			return @file_put_contents(DOCROOT.'/.htaccess', $htaccess);
		}

		private function __createLanguageRules($htaccess){
			$rule = "\t### LANGUAGE REDIRECT RULES start\n\t### no language codes set\n\t### LANGUAGE REDIRECT RULES end";

			## Remove existing rules
			$htaccess = $this->__removeLanguageRules($htaccess);

			$htaccess = preg_replace('/(\s?### FRONTEND REWRITE)/', "{$rule}\n\n$1", $htaccess);

			return $htaccess;
		}

		private function __editLanguageRules($htaccess, $langs = array()){
			## Cannot use $1 in a preg_replace replacement string, so using a token instead
			$token_language = md5('language');
			$token_region = md5('region');
			$token_symphony = md5('symphony-page');

			if( !empty($langs) ){
				$languages = array();
				$regions = array();
				foreach( $langs as $lang_code ){
					$languages[] = substr($lang_code, 0, 2);
					$regions[] = substr(strrchr($lang_code, '-'), 1);
				}
				$languages = array_filter(array_unique($languages));
				$regions = array_filter(array_unique($regions));

				$languages = (is_array($languages) and !empty($languages)) ? implode('|', $languages) : NULL;
				$regions = (is_array($regions) and !empty($regions)) ? implode('|', $regions) : NULL;

				$rule = "\n\tRewriteCond %{REQUEST_FILENAME} !-d";
				$rule .= "\n\tRewriteCond %{REQUEST_FILENAME} !-f";
				$rule .= "\n\tRewriteRule ^({$languages})-?({$regions})?\/(.*\/?)$ index.php?fl-language={$token_language}&fl-region={$token_region}&symphony-page={$token_symphony}&%{QUERY_STRING} [L]";
			} else{
				$rule = "\n\t### no language codes set";
			}

			$htaccess = preg_replace('/(\s+### LANGUAGE REDIRECT RULES start)(.*?)(\s*### LANGUAGE REDIRECT RULES end)/s', "$1{$rule}$3", $htaccess);

			## Replace the token with the real value
			$htaccess = str_replace($token_language, '$1', $htaccess);
			$htaccess = str_replace($token_region, '$2', $htaccess);
			$htaccess = str_replace($token_symphony, '$3', $htaccess);

			return $htaccess;
		}

		private function __removeLanguageRules($htaccess){
			return preg_replace('/\s+### LANGUAGE REDIRECT RULES start(.*?)### LANGUAGE REDIRECT RULES end/s', NULL, $htaccess);
		}

	}


<?php

namespace Pforret\PhpOutlookSignature;

use Exception;

class PhpOutlookSignature
{
    private string $default_template = "";
    private string $template_folder = "";    //<template>
    private string $template_file = "";      //<template>/<message>.htm
    private string $assets_folder = "";      //<template>/<message>_files/
    private string $assets_local = "";       //<message>_files
    private string $last_error = '';
    private array $keywords = [];
    private array $included_files = [];
    private bool $is_ready = false;

    public function __construct(string $folder = "")
    {
        $this->is_ready = false;
        $this->last_error = "";
        $this->default_template = "__DIR__/templates/default";
        if (! file_exists($this->default_template)) {
            $this->last_error = sprintf("Default template folder [%s] does not exist", $this->default_template);

            throw new Exception(sprintf("Default template folder [%s] does not exist", $this->default_template));
        }
        if (! $folder) {
            $folder = $this->default_template;
        }
        if (! file_exists($this->template_folder)) {
            $this->last_error = sprintf("Template folder [%s] does not exist", $this->template_folder);

            throw new Exception(sprintf("Template folder [%s] does not exist", $this->template_folder));
        }
        $this->template_folder = $folder;
        $this->check_template_files($this->template_folder);
        if (! file_exists($this->assets_folder."/filelist.xml")) {
            $this->create_filelist();
        }
        $this->analyze_template_text();
        $this->is_ready = true;
    }

    public function get_error(): string
    {
        return $this->last_error;
    }

    private function check_template_files(string $folder): bool
    {
        $html_files = glob("$folder/*.htm");
        if (count($html_files) === 0) {
            throw new Exception(sprintf("Template folder [%s] does not contain a .htm file", $this->template_folder));
        }
        if (count($html_files) > 1) {
            throw new Exception(sprintf("Template folder [%s] should only contain 1 .htm file (now: %d)", $this->template_folder, count($html_files)));
        }

        $this->template_file = $html_files[0];
        $template_name = basename($this->template_file, ".htm");
        $assets_folder = sprintf("%s/%s", $this->template_folder, "${template_name}_files");
        if (! is_dir($assets_folder)) {
            // TODO: look for alternative folder name in template
            throw new Exception(sprintf("Template assets folder [%s] cannot be found", $assets_folder));
            // most probable
        }
        $this->assets_folder = $assets_folder;
        $this->assets_local = basename($assets_folder);

        return true;
    }

    private function analyze_template_text(): bool
    {
        $this->keywords = [];
        $this->included_files = [];
        $text = file_get_contents($this->template_file);
        $text = str_replace($this->assets_local."/", "{assets}/", $text);
        preg_match_all("|\{(\w+)\}|", $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $keyword = $match[1];
            $this->keywords[$keyword] = $keyword;
        }
        preg_match_all("|({assets}/\w+\.\w+)|", $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $file = $match[1];
            $file = str_replace("{assets}", $this->assets_folder, $file);
            $this->included_files[$file] = $file;
        }

        return true;
    }

    public function get_keywords(): array
    {
        return array_keys($this->keywords);
    }

    public function get_assets(): array
    {
        return array_keys($this->included_files);
    }

    public function create(string $output_file, array $values, bool $ignore_errors = false): bool
    {
        $output_folder = dirname($output_file);
        $output_name = pathinfo($output_file, PATHINFO_FILENAME);
        $assets_folder_name = "${output_name}_files";
        $assets_folder = "$output_folder/$assets_folder_name";
        if (! file_exists($output_folder)) {
            mkdir($output_folder, 0777, true);
        }
        if (! file_exists($assets_folder)) {
            mkdir($assets_folder, 0777, true);
        }
        if (! isset($values["assets"])) {
            $values["assets"] = $assets_folder_name;
        }
        // fill in template

        $text = file_get_contents($this->template_file);
        foreach ($this->keywords as $keyword) {
            if (! $ignore_errors && ! isset($values[$keyword])) {
                throw new Exception("Template expects [$keyword] but none was given");
            }
            $value = isset($values[$keyword]) ? $values[$keyword] : "";
            $text = str_replace('{' . $keyword . '}', $value, $text);
        }
        // save files
        file_put_contents($output_file, $text);
        foreach ($this->included_files as $file) {
            $file = str_replace("{assets}", $values["assets"], $file);
            $copy_file = "$assets_folder/" . basename($file);
            copy($file, $copy_file);
        }
        $install_template = $this->template_folder."/install_signature.cmd";
        $install_script = $output_folder."/".basename($install_template);
        if (file_exists($install_template)) {
            $script = file_get_contents($install_template);
            $script = str_replace("{source}", "$output_folder", $script);
            $script = str_replace("{destin}", "%APPDATA%\Microsoft\Signatures", $script);
            file_put_contents($install_script, $script);
        }

        return true;
    }

    private function create_filelist(): bool
    {
        $xml = '<xml xmlns:o="urn:schemas-microsoft-com:office:office">' . PHP_EOL;
        $xml .= sprintf(' <o:MainFile HRef="../%s"/>', basename($this->template_file)) . PHP_EOL;
        $assets = glob($this->assets_folder . "/*");
        foreach ($assets as $asset) {
            $xml .= sprintf(' <o:File HRef="%s"/>', basename($asset)) . PHP_EOL;
        }
        $xml .= '</xml>' . PHP_EOL;
        $filelist = "$this->assets_folder/filelist.xml";
        file_put_contents($filelist, $xml);
        $this->included_files[$filelist] = $filelist;

        return true;
    }
}

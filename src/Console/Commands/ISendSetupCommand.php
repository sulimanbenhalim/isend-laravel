<?php

namespace ISend\SMS\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ISendSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'isend:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup iSend SMS for Laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayLogo();
        
        $this->info('Welcome to iSend SMS for Laravel!');
        $this->newLine();
        $this->info('This command will help you set up your iSend SMS configuration.');
        $this->newLine();
        
        // Get API key
        $apiToken = $this->secret('Please enter your iSend API token');
        
        while (empty($apiToken)) {
            $this->error('API token cannot be empty');
            $apiToken = $this->secret('Please enter your iSend API token');
        }
        
        // Get default sender ID (optional)
        $senderId = $this->ask('Please enter your default sender ID (leave empty to skip)');
        
        // Ask for customization of base URL (advanced)
        $customizeBaseUrl = $this->confirm('Do you want to customize the base URL? (default: https://isend.com.ly)');
        $baseUrl = 'https://isend.com.ly';
        
        if ($customizeBaseUrl) {
            $baseUrl = $this->ask('Enter the base URL', 'https://isend.com.ly');
        }
        
        // Update environment file
        $this->updateEnvironmentFile($apiToken, $senderId, $baseUrl);
        
        // Publish the config
        $this->call('vendor:publish', [
            '--tag' => 'isend-config',
        ]);
        
        $this->newLine();
        $this->info('✅ iSend SMS has been successfully configured!');
        $this->info('You can now start sending SMS messages through the iSend API.');
        $this->newLine();
        $this->comment('To learn more, check out the documentation at:');
        $this->comment('https://github.com/sulimanbenhalim/isend-laravel');
    }
    
    /**
     * Display the iSend ASCII logo.
     */
    protected function displayLogo()
    {
        // Color codes
        $blue = "\e[34m";
        $green = "\e[32m";
        $reset = "\e[0m";
        
        $logo = <<<EOT
        {$blue}
        ██╗███████╗███████╗███╗   ██╗██████╗ 
        ██║██╔════╝██╔════╝████╗  ██║██╔══██╗
        ██║███████╗█████╗  ██╔██╗ ██║██║  ██║
        ██║╚════██║██╔══╝  ██║╚██╗██║██║  ██║
        ██║███████║███████╗██║ ╚████║██████╔╝
        ╚═╝╚══════╝╚══════╝╚═╝  ╚═══╝╚═════╝ 
                                           
        {$green}SMS for Laravel{$reset}
        EOT;
        
        $this->line($logo);
        $this->newLine();
    }
    
    /**
     * Update the environment file with the new values.
     */
    protected function updateEnvironmentFile(string $apiToken, ?string $senderId, string $baseUrl)
    {
        $envFile = app()->environmentFilePath();
        
        if (!File::exists($envFile)) {
            $this->error('.env file not found');
            return;
        }
        
        $envContents = File::get($envFile);
        
        // Update or add API token
        if (strpos($envContents, 'ISEND_API_TOKEN=') !== false) {
            $envContents = preg_replace('/ISEND_API_TOKEN=.*/', 'ISEND_API_TOKEN='.$apiToken, $envContents);
        } else {
            $envContents .= PHP_EOL.'ISEND_API_TOKEN='.$apiToken;
        }
        
        // Update or add sender ID if provided
        if (!empty($senderId)) {
            if (strpos($envContents, 'ISEND_DEFAULT_SENDER_ID=') !== false) {
                $envContents = preg_replace('/ISEND_DEFAULT_SENDER_ID=.*/', 'ISEND_DEFAULT_SENDER_ID='.$senderId, $envContents);
            } else {
                $envContents .= PHP_EOL.'ISEND_DEFAULT_SENDER_ID='.$senderId;
            }
        }
        
        // Update or add base URL if different from default
        if ($baseUrl !== 'https://isend.com.ly') {
            if (strpos($envContents, 'ISEND_BASE_URL=') !== false) {
                $envContents = preg_replace('/ISEND_BASE_URL=.*/', 'ISEND_BASE_URL='.$baseUrl, $envContents);
            } else {
                $envContents .= PHP_EOL.'ISEND_BASE_URL='.$baseUrl;
            }
        }
        
        File::put($envFile, $envContents);
        
        $this->info('Environment file updated successfully.');
    }
}
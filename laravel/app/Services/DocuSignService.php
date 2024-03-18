<?php

namespace App\Services;

use Exception;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Model\RecipientViewRequest;

class DocuSignService
{

    private $config;
   
      
    private $args;
   
      
    private $signer_client_id = 1000;

    public function connectDocusign()
    {
        try {
            $params = [
                'response_type' => 'code',
                'scope' => 'signature impersonation',
                'client_id' => config('docusign.client_id'),
                'redirect_uri' => 'http://localhost',
            ];
            $queryBuild = http_build_query($params);
  
            $url = config('docusign.oauth_url') . "/auth?";
  
            $botUrl = $url . $queryBuild;
            return redirect()->to($botUrl);
        } catch (Exception $e) {
            dd('error');
        }
    }   

    public function signDocument()
    {
        try {
            $this->args = $this->getTemplateArgs();
  
            $envelope_args = $this->args["envelope_args"];
              
            $envelope_definition = $this->makeEnvelopeFileObject($this->args["envelope_args"]);
            $envelope_api = $this->getEnvelopeApi();
  
            $api_client = new ApiClient($this->config);
            $envelope_api = new EnvelopesApi($api_client);
            $results = $envelope_api->createEnvelope($this->args['account_id'], $envelope_definition);
            $envelopeId = $results->getEnvelopeId();
  
            $authentication_method = 'None';
            // $user = auth()->user();
            $recipient_view_request = new RecipientViewRequest([
                'authentication_method' => $authentication_method,
                'client_user_id' => $envelope_args['signer_client_id'],
                // 'recipient_id' => '1',
                'return_url' => $envelope_args['ds_return_url'],
                'user_name' => 'Test aaa', 
                'email' => 'test@example.com'
            ]);
  
            $results = $envelope_api->createRecipientView(
                $this->args['account_id'], 
                $envelopeId, 
                $recipient_view_request
            );
  
            return [
                'error' => null,
                'url' => $results['url'],
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'url' => null,
            ];
        }
    }
  
    private function makeEnvelopeFileObject($args)
    {
        $docsFilePath = storage_path('app/pdf/test.pdf');
  
        $arrContextOptions=[
            "ssl"=>[
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ],
        ];
 
        $contentBytes = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));

        $document = new \DocuSign\eSign\Model\Document([
            'document_base64' => base64_encode($contentBytes),
            'name' => 'NDA',
            'file_extension' => 'pdf',
            'document_id' => 1
        ]);

        // $user = auth()->user();
        $signer = new \DocuSign\eSign\Model\Signer([
            'email' => 'test@example.com',
            'name' => 'Test aaa',
            'recipient_id' => '1',
            'routing_order' => '1',
            'client_user_id' => $args['signer_client_id']
        ]);
  
        $signHere = new \DocuSign\eSign\Model\SignHere([
            'anchor_string' => '/sn1/',
            'anchor_units' => 'pixels',
            'anchor_y_offset' => '10',
            'anchor_x_offset' => '20',
        ]);

        $dateSigned = new \DocuSign\eSign\Model\DateSigned([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '300',
            'y_position' => '185'
        ]);

        $signer->settabs(new \DocuSign\eSign\Model\Tabs([
            'sign_here_tabs' => [$signHere],
            'date_signed_tabs' => [$dateSigned]
        ]));
 
        $envelopeDefinition = new \DocuSign\eSign\Model\EnvelopeDefinition([
            'email_subject' => "Please sign this document",
            'documents' => [$document],
            'recipients' => new \DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),
            'status' => "sent",
        ]);
 
        return $envelopeDefinition;
    }
  
    public function getEnvelopeApi(): EnvelopesApi
    {
        $this->config = new Configuration();
        $this->config->setHost($this->args['base_path']);
        $this->config->addDefaultHeader('Authorization', 'Bearer ' . $this->args['ds_access_token']);
        $this->apiClient = new ApiClient($this->config);
  
        return new EnvelopesApi($this->apiClient);
    }
  
    private function getTemplateArgs()
    {
        $args = [
            'account_id' => config('docusign.account_id'),
            'base_path' => config('docusign.base_url'),
            'ds_access_token' => $this->getAccessToken(),
            'envelope_args' => [
                'signer_client_id' => $this->signer_client_id,
                'ds_return_url' => 'http://localhost'
            ]
        ];
           
        return $args;
    }

    private function getAccessToken()
    {
        try {
            $apiClient = new ApiClient();
            $apiClient->getOAuth()->setOAuthBasePath(config('docusign.oauth_base'));
            $response = $apiClient->requestJWTUserToken(
                $ikey = config('docusign.client_id'),
                $userId = config('docusign.user_id'),
                $key = file_get_contents(storage_path('app/key/private.key')),
                $scope = "signature impersonation"
            );
            $token = $response[0];
            $accessToken = $token->getAccessToken();
        } catch (\Throwable $th) {
            throw $th;
        }
        return $accessToken;
    }
}
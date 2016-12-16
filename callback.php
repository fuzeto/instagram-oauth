public function callbackAction(Request $request)
{
    $code = $request->get('code');

    if (!isset($code)) {
        return $this->redirect($this->generateUrl('frontend_index').'?socialError=4');
    }

    $instagram = new Instagram([
        'clientId'      => $this->consumerId,
        'clientSecret'  => $this->consumerSecret,
        'redirectUri'   => 'http://example.example.com'
    ]);

    try {
        $token = $instagram->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch (\Exception $e) {
        $this->logger->addCritical($e->getMessage());
        $this->redirect($this->generateUrl('frontend_index').'?socialError=4');
    }

    if (!isset($token)) {
        return $this->redirect($this->generateUrl('frontend_index').'?socialError=4');
    }

    $profileInfos = $token->getValues();
    $user         = $profileInfos['user'];

    $this->session->set(
        'guest',
        [
            'data' =>
            [
                'id'     => null,
                'name'   => $user['full_name'],
                'locale' => 'pt_br',
                'gender' => null,
                'email'  => null
            ],
            'social' =>
            [
                'id'   => $user['id'],
                'type' => Social::INSTAGRAM
            ]
        ]
    );

    return $this->redirect(
        $this->generateUrl('complete_registration')
    );
}

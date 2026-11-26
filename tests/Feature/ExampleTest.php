<?php

test('the root URL redirects to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

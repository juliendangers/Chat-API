<?php

namespace WhatsApp\ChatApi;

interface NewMsgBindInterface
{
    public function process(ProtocolNode $node);
}

<?php

namespace Acquia\Network;

final class XmlrpcError
{
    const NOT_FOUND        = 1000;
    const KEY_MISMATCH     = 1100;
    const EXPIRED          = 1200;
    const REPLAY_ATTACK    = 1300;
    const KEY_NOT_FOUND    = 1400;
    const MESSAGE_FUTURE   = 1500;
    const MESSAGE_EXPIRED  = 1600;
    const MESSAGE_INVALID  = 1700;
    const VALIDATION_ERROR = 1800;
    const PROVISION_ERROR  = 9000;
}

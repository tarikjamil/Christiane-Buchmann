<?php
// Requests normally never reach this file: cb_handle_request() renders them on parse_request.
cb_dispatch(cb_request_path());

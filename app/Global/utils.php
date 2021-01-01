<?php
function dd_json($data) {
    return response()->json($data, 500);
}

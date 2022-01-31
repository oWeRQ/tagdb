export default function(reason) {
    let controller;

    return function() {
        if (controller) {
            controller.abort(reason);
        }

        controller = new AbortController();

        return controller.signal;
    }
}

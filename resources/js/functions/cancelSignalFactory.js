export default function() {
    let controller;

    return function(reason) {
        if (controller) {
            controller.abort(reason);
        }

        controller = new AbortController();

        return controller.signal;
    }
}

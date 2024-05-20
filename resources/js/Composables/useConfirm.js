import {reactive, readonly} from "vue";

const globalState = reactive({
    show: false,
    title: '',
    message: '',
    resolver: null,
});

export default function useConfirm() {
    const reset = () => {
        globalState.show = false;
        globalState.title = '';
        globalState.message = '';
        globalState.resolver = null;
    }

    return {
        state: readonly(globalState),
        confirmation: (message, title = 'Please Confirm') => {
            globalState.show = true;
            globalState.title = title;
            globalState.message = message;

            return new Promise((resolver) => {
                globalState.resolver = resolver;
            });
        },
        confirm: () => {
            if (globalState.resolver) {
                globalState.resolver(true);
            }

            reset();
        },
        cancel: () => {
            if (globalState.resolver) {
                globalState.resolver(false);
            }

            reset();
        }
    }
}

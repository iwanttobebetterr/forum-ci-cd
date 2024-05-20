<template>
    <AppLayout :title="post.title">
        <Container>
            <PageHeading>{{ post.title }}</PageHeading>
            <span class="block mt-1 text-sm text-gray-600">{{ relativeDate(post.created_at) }} ago by {{post.user.name}}</span>
            <article class="mt-6 prose prose-sm max-w-none" v-html="post.html"></article>

            <div class="mt-4">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form class="mt-4" @submit.prevent="() => commentIdBeingEdited ? updateComment() : addComment()">
                    <div>
                        <InputLabel for="body" value="Comment" class="sr-only"/>
                        <MarkdownEditor ref="commentTextAreaRef" id="body" v-model="commentForm.body" editor-class="min-h-[160px]" placeholder="Write your comment"/>
                        <InputError :message="commentForm.errors.body" class="mt-1"/>
                    </div>

                    <PrimaryButton
                        class="mt-2" :disabled="commentForm.processing"
                        v-text="commentIdBeingEdited ? 'Update' : 'Comment'"
                    />
                    <SecondaryButton
                        v-if="commentIdBeingEdited"
                        @click="commentIdBeingEdited = null; commentForm.reset()"
                        class="mt-2 ml-2"
                    >Cancel</SecondaryButton>
                </form>

                <ul class="divide-y mt-4">
                    <li v-for="comment in comments.data" :key="comment.id" class="px-2 py-4">
                        <Comment :comment="comment" @delete="deleteComment" @edit="editComment"/>
                    </li>
                </ul>

                <Pagination :meta="comments.meta" :only="['comments']"></Pagination>
            </div>
        </Container>
    </AppLayout>
</template>

<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {router, useForm} from "@inertiajs/vue3";
import {relativeDate} from "@/Utilities/date.js";
import Comment from "@/Components/Comment.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import {computed, ref} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import useConfirm from "@/Composables/useConfirm.js";
import MarkdownEditor from "@/Components/MarkdownEditor.vue";
import PageHeading from "@/Components/PageHeading.vue";

const props = defineProps({
    post: Object,
    comments: Object,
})

const commentForm = useForm({
    body: '',
})

const addComment = () => commentForm.post(route('posts.comments.store', props.post.id), {
    preserveScroll: true,
    onSuccess: () => commentForm.reset(),
})

const {confirmation} = useConfirm();

const updateComment = async () => {
    if (! await confirmation('Are you sure you want to edit this comment?')) {
        commentTextAreaRef.value?.focus();
        return;
    }

    commentForm.put(route('comments.update', {
        comment: commentIdBeingEdited.value,
        page: props.comments.meta.current_page,
    }), {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    })
}


const deleteComment = async (commentId) => {
    if (! await confirmation('Are you sure you want to delete this comment?')) {
        return;
    }

    router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page }), {
        preserveScroll: true,
    })
};

const commentTextAreaRef = ref(null);
const commentIdBeingEdited = ref(null);
const commentBeingEdited = computed(() => props.comments.data.find(comment => comment.id === commentIdBeingEdited.value));
const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId;
    commentForm.body = commentBeingEdited.value?.body;
    commentTextAreaRef.value?.focus();
};

</script>

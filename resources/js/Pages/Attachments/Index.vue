<template>
    <div>
        <h1>Attachments</h1>
        <div>
            <input
                v-model="filters.q"
                @input="applyFilter"
                placeholder="Search file path..."
            />
            <select v-model="filters.sort" @change="applyFilter">
                <option value="created_at">Created At</option>
                <option value="uploaded_at">Uploaded At</option>
            </select>
            <select v-model="filters.order" @change="applyFilter">
                <option value="desc">Desc</option>
                <option value="asc">Asc</option>
            </select>
            <button @click="toggleTrash">
                {{ filters.trash == 1 ? "Show Active" : "Show Trash" }}
            </button>
            <Link href="/attachments/create">+ Add Attachment</Link>
        </div>
        <table>
            <thead>
                <tr>
                    <th>File</th>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Is Public?</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in attachments.data" :key="a.id">
                    <td>
                        <a :href="`/storage/${a.file_path}`" target="_blank">{{
                            a.file_path
                        }}</a>
                    </td>
                    <td>{{ a.task?.title }}</td>
                    <td>{{ a.task?.project?.name }}</td>
                    <td>{{ a.is_public ? "Yes" : "No" }}</td>
                    <td>{{ a.uploaded_at ?? "-" }}</td>
                    <td v-if="filters.trash != 1">
                        <Link :href="`/attachments/${a.id}/edit`">Edit</Link>
                        <form
                            :action="`/attachments/${a.id}`"
                            method="POST"
                            @submit.prevent="deleteAttachment(a.id)"
                        >
                            <input
                                type="hidden"
                                name="_method"
                                value="DELETE"
                            />
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                    <td v-else>
                        <button @click="restoreAttachment(a.id)">
                            Restore
                        </button>
                        <form
                            :action="`/attachments/${a.id}/force-delete`"
                            method="POST"
                            @submit.prevent="confirmDeletePermanently(a.id)"
                        >
                            <input
                                type="hidden"
                                name="_method"
                                value="DELETE"
                            />
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                    <td>
                        <Link :href="`/attachments/${a.id}`">History</Link>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="attachments.links.length > 3">
            <button
                v-for="l in attachments.links"
                :key="l.url"
                :disabled="!l.url"
                @click="goTo(l.url)"
                v-html="l.label"
            />
        </div>
    </div>
</template>
<script setup>
import { Link, router } from "@inertiajs/vue3";
import { reactive } from "vue";
const props = defineProps({ attachments: Object, filters: Object });
const filters = reactive({ ...props.filters });
function applyFilter() {
    router.get("/attachments", filters, { preserveState: true, replace: true });
}
function toggleTrash() {
    filters.trash = filters.trash == 1 ? 0 : 1;
    applyFilter();
}
function goTo(url) {
    router.visit(url);
}
function deleteAttachment(id) {
    if (confirm("Delete this attachment?")) {
        router.delete(`/attachments/${id}`);
    }
}

function restoreAttachment(id) {
    if (confirm("Restore this attachment?")) {
        toggleTrash();
        router.post(`/attachments/${id}/restore`);
    }
}

function confirmDeletePermanently(id) {
    if (confirm("Delete permanently this attachment?")) {
        toggleTrash();
        router.delete(`/attachments/${id}/force-delete`);
    }
}
</script>

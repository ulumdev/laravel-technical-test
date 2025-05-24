<template>
    <div>
        <h1>Tasks</h1>
        <div>
            <input
                v-model="filters.query"
                @input="applyFilter"
                placeholder="Search title..."
            />
            <select v-model="filters.sort" @change="applyFilter">
                <option value="created_at">Created At</option>
                <option value="title">Title</option>
                <option value="due_date">Due Date</option>
            </select>
            <select v-model="filters.order" @change="applyFilter">
                <option value="desc">Desc</option>
                <option value="asc">Asc</option>
            </select>
            <button @click="toggleTrash">
                {{ filters.trash == 1 ? "Show Active" : "Show Trash" }}
            </button>
            <Link href="/tasks/create">+ Add Task</Link>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Project</th>
                    <th>Done?</th>
                    <th>Due Date</th>
                    <th>Attachments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="task in tasks.data" :key="task.id">
                    <td>{{ task.title }}</td>
                    <td>{{ task.project?.name }}</td>
                    <td>{{ task.is_done ? "Yes" : "No" }}</td>
                    <td>{{ task.due_date ?? "-" }}</td>
                    <td>{{ task.attachments?.length ?? 0 }}</td>
                    <td v-if="filters.trash != 1">
                        <Link :href="`/tasks/${task.id}/edit`">Edit</Link>
                        <form
                            :action="`/tasks/${task.id}`"
                            method="POST"
                            @submit.prevent="deleteTask(task.id)"
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
                        <button @click="restoreTask(task.id)">Restore</button>
                        <form
                            :action="`/tasks/${task.id}/force-delete`"
                            method="POST"
                            @submit.prevent="confirmDeletePermanently(task.id)"
                        >
                            <input
                                type="hidden"
                                name="_method"
                                value="DELETE"
                            />
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="tasks.links.length > 3">
            <button
                v-for="link in tasks.links"
                :key="link.url"
                :disabled="!link.url"
                @click="goTo(link.url)"
                v-html="link.label"
            />
        </div>
    </div>
</template>
<script setup>
import { Link, router } from "@inertiajs/vue3";
import { reactive } from "vue";
const props = defineProps({ tasks: Object, filters: Object });
const filters = reactive({ ...props.filters });
function applyFilter() {
    router.get("/tasks", filters, { preserveState: true, replace: true });
}
function toggleTrash() {
    filters.trash = filters.trash == 1 ? 0 : 1;
    applyFilter();
}

function goTo(url) {
    router.visit(url);
}

function deleteTask(id) {
    if (confirm("Delete this task?")) {
        router.delete(`/tasks/${id}`);
    }
}

function restoreTask(id) {
    if (confirm("Restore this task?")) {
        toggleTrash();
        router.post(`/tasks/${id}/restore`);
    }
}

function confirmDeletePermanently(id) {
    if (confirm("Delete permanently this task?")) {
        toggleTrash();
        router.delete(`/tasks/${id}/force-delete`);
    }
}
</script>

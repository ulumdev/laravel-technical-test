<template>
    <div>
        <h2>Projects</h2>
        <div>
            <input
                v-model="filters.query"
                placeholder="Search name..."
                @input="applyFilter"
            />
            <select v-model="filters.sort" @change="applyFilter">
                <option value="created_at">Created At</option>
                <option value="name">Name</option>
                <option value="start_date">Start Date</option>
            </select>
            <select v-model="filters.order" @change="applyFilter">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
            <button @click="toogleTrash">
                {{ filters.trash == 1 ? "Show Active" : "Show Trash" }}
            </button>
            <Link href="/projects/create">+ Add Project</Link>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Active</th>
                    <th>Start Date</th>
                    <th>Tasks Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="project in projects.data" :key="project.id">
                    <td>{{ project.name }}</td>
                    <td>{{ project.is_active ? "Yes" : "No" }}</td>
                    <td>{{ project.start_date ?? "-" }}</td>
                    <td>{{ project.tasks_count }}</td>
                    <td v-if="filters.trash != 1">
                        <Link :href="`/projects/${project.id}/edit`">Edit</Link>
                        <form
                            :action="`/projects/${project.id}`"
                            method="POST"
                            @submit.prevent="deleteProject(project.id)"
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
                        <button @click="restoreProject(project.id)">
                            Restore
                        </button>
                        <form
                            :action="`/projects/${project.id}/force-delete`"
                            method="POST"
                            @submit.prevent="
                                confirmDeletePermanently(project.id)
                            "
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
        <div v-if="projects.links.length > 3">
            <button
                v-for="link in projects.links"
                :key="link.url"
                :disabled="!link.url"
                @click="goTo(link.url)"
                v-html="link.label"
            />
        </div>
        <Link :href="`/projects/create`">Add Project</Link>
    </div>
</template>

<script setup>
import { reactive } from "vue";
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
    projects: Object,
    filters: Object,
});
const filters = reactive({ ...props.filters });

function applyFilter() {
    router.get("/projects", filters, { preserveState: true, replace: true });
}

function toogleTrash() {
    filters.trash = filters.trash == 1 ? 0 : 1;
    applyFilter();
}

function goTo(url) {
    router.visit(url);
}

function deleteProject(id) {
    if (confirm("Delete this project?")) {
        router.delete(`/projects/${id}`);
    }
}

function restoreProject(id) {
    if (confirm("Restore this project?")) {
        toogleTrash();
        router.post(`/projects/${id}/restore`);
    }
}

function confirmDeletePermanently(id) {
    if (confirm("Delete permanently this project?")) {
        toogleTrash();
        router.delete(`/projects/${id}/force-delete`);
    }
}
</script>

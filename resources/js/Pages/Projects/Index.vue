<template>
    <h2>Projects</h2>
    <ExportForm :fields="fields" entity="project" />
    <ImportForm :dbFields="fields" entity="project" />

    <!-- Daftar Import Jobs -->
    <div v-if="importJobs.length" style="margin: 1.5em 0">
        <h3>Daftar Import Terakhir</h3>
        <ul>
            <li v-for="job in importJobs" :key="job.id">
                [{{ job.status }}] {{ job.file_name }}
                <span v-if="job.status === 'completed'">
                    (Import selesai!)</span
                >
                <span v-else-if="job.status === 'failed'">
                    (Gagal: {{ job.message }})</span
                >
                <span v-else> (sedang diproses...)</span>
            </li>
        </ul>
    </div>

    <!-- Tambahkan daftar export jobs -->
    <div v-if="exportJobs.length" style="margin: 1.5em 0">
        <h3>Daftar Export Terakhir</h3>
        <ul>
            <li v-for="job in exportJobs" :key="job.id">
                [{{ job.status }}] {{ job.file_name }}
                <a
                    v-if="job.status === 'done' || job.status === 'completed'"
                    :href="`/download-export?job_id=${job.id}`"
                    target="_blank"
                    >Download</a
                >
                <span v-else> (sedang diproses...)</span>
            </li>
        </ul>
    </div>

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
        <button @click="toggleTrash">
            {{ filters.trash == 1 ? "Show Active" : "Show Trash" }}
        </button>
        <Link href="/projects/create">+ Add Project</Link>
    </div>
    <!-- <form @submit.prevent="exportExcel">
        <label v-for="f in fields" :key="f">
            <input type="checkbox" v-model="selectedFields" :value="f" />
            {{ f }}
        </label>
        <button type="submit">Export</button>
    </form> -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Active</th>
                <th>Start Date</th>
                <th>Tasks Count</th>
                <th>Action</th>
                <th>History</th>
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
                        <input type="hidden" name="_method" value="DELETE" />
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td v-else>
                    <button @click="restoreProject(project.id)">Restore</button>
                    <form
                        :action="`/projects/${project.id}/force-delete`"
                        method="POST"
                        @submit.prevent="confirmDeletePermanently(project.id)"
                    >
                        <input type="hidden" name="_method" value="DELETE" />
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <Link :href="`/projects/${project.id}`">History</Link>
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
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ExportForm from "./ExportForm.vue";
import ImportForm from "./ImportForm.vue";

const fields = ["id", "name", "details", "is_active", "start_date"];
const exportJobs = ref([]);
const lastExportJobId = ref(null);
const alreadyDownloadsJobIds = ref([]);

const importJobs = ref([]);
const lastImportJobId = ref(null);

function fetchJobs() {
    fetch("/export-jobs")
        .then((res) => res.json())
        .then((jobs) => {
            exportJobs.value = jobs;

            if (lastExportJobId.value) {
                const job = jobs.find((j) => j.id === lastExportJobId.value);
                if (
                    job &&
                    (job.status === "done" || job.status === "completed") &&
                    !alreadyDownloadsJobIds.value.includes(job.id)
                ) {
                    downloadExport(job.id);
                    alreadyDownloadsJobIds.value.push(job.id);
                }
            }
        });
}

function fetchImportJobs() {
    fetch("/import-jobs")
        .then((res) => res.json())
        .then((jobs) => {
            importJobs.value = jobs;
        });
}

function onExported(job_id) {
    lastExportJobId.value = job_id;
    fetchJobs();
}

function onImported(job_id) {
    lastImportJobId.value = job_id;
    fetchImportJobs();
}

function downloadExport(jobId) {
    window.open(`/download-export?job_id=${jobId}`, "_blank");
}

// pooling setiap 5 detik
onMounted(() => {
    fetchJobs();
    fetchImportJobs();
    setInterval(fetchJobs, 5000);
    setInterval(fetchImportJobs, 5000);
});

const props = defineProps({
    projects: Object,
    filters: Object,
});
const filters = reactive({ ...props.filters });

// const selectedFields = ref([...props.fields]);
// function exportExcel() {
//     window.location = `/${
//         props.entity
//     }/export?fields=${selectedFields.value.join(",")}`;
// }

function applyFilter() {
    router.get("/projects", filters, { preserveState: true, replace: true });
}

function toggleTrash() {
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
        toggleTrash();
        router.post(`/projects/${id}/restore`);
    }
}

function confirmDeletePermanently(id) {
    if (confirm("Delete permanently this project?")) {
        toggleTrash();
        router.delete(`/projects/${id}/force-delete`);
    }
}
</script>

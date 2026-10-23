<?php

class TaskManager
{
    private string $filePath;
    private array $tasks = [];
    
    public function __construct(string $filePath = __DIR__ . '/../tasks.json') {
        $this->filePath = $filePath;
        $this->loadTasks();
    }

    //muat task dari json (buat file jika belum ada)
    private function loadTasks() : void 
    {
        if(!file_exists($this->filePath)) {
            $this->tasks = [];
            $this->saveTasks();
            return;
        }    

        $content = file_get_contents($this->filePath);
        if ($content === false) {
            fwrite(STDERR, "Gagal membaca file tasks.json\n");
            exit(1);
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            fwrite(STDERR, "Format tasks.json tidak valid. Membuat ulang..\n");
            $this->tasks = [];
            $this->saveTasks();
        }

        $this->tasks = $decoded;
    }

    //simpan tasks ke json dengan pretty print
    private function saveTasks(): void
    {
        $json = json_decode(
            array_values($this->tasks),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if (file_put_contents($this->filePath, $json . PHP_EOL) === false) {
            fwrite(STDERR, "Gagal menulis ke tasks.json\n");
            exit(1);
        }
    }

    //dapatkan id berikutnya
    private function nextId(): int
    {
        if (empty($this->tasks)) {
            return 1;
        }
        return max(array_column($this->tasks, 'id')) + 1;
    }

    // cari index task berdasarkan id
    private function findIndexById(int $id): ?int
    {
        foreach ($this->tasks as $index => $task) {
            if ($task['id'] === $id) {
                return $index;
            }
        }
        return null;
    }

    public function add(string $description) : void 
    {
        $description = trim($description);
        if ($description === '') {
            fwrite(STDERR, "Error: Deskripsi task tidak boleh kosong.\n");
            exit(1);
        }

        $id = $this->nextId();
        $now = date('c'); //ISO 8601
    }
}
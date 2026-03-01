<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!is_logged_in()) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_note') {
        $note = $_POST['note'] ?? '';
        $priority = $_POST['priority'] ?? 'normal';
        $saved = $user_id ? add_war_room_note($user_id, $note, $priority) : false;
        $flash = $saved ? ['type' => 'success', 'msg' => 'Task added to war room.'] : ['type' => 'error', 'msg' => 'Failed to add task.'];
    }

    if ($action === 'update_status') {
        $note_id = (int) ($_POST['note_id'] ?? 0);
        $status = $_POST['status'] ?? 'open';
        $saved = $user_id ? update_war_room_note_status($user_id, $note_id, $status) : false;
        $flash = $saved ? ['type' => 'success', 'msg' => 'Task status updated.'] : ['type' => 'error', 'msg' => 'Failed to update task status.'];
    }
}

$notes = $user_id ? get_war_room_notes($user_id, 50) : [];
$priority_colors = [
    'critical' => 'text-red-300 border-red-800 bg-red-900/20',
    'high' => 'text-orange-300 border-orange-800 bg-orange-900/20',
    'normal' => 'text-blue-300 border-blue-800 bg-blue-900/20',
    'low' => 'text-neutral-300 border-neutral-700 bg-neutral-900/20',
];
?>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm inline-flex items-center gap-1">&larr; Back to Mission Control</a>
        <h1 class="text-3xl font-bold text-white mt-3">Team Collaboration War Room</h1>
        <p class="text-neutral-400 mt-2">Track investigation tasks, priorities, and live status during CTF operations.</p>
    </div>

    <?php if ($flash): ?>
        <div class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'border border-green-800 bg-green-900/20 text-green-300' : 'border border-red-800 bg-red-900/20 text-red-300' ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <h2 class="text-lg text-white font-bold mb-4">Add Task</h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_note">
                <div>
                    <label class="block text-xs text-neutral-500 uppercase mb-2">Task</label>
                    <textarea name="note" rows="4" maxlength="500" required class="w-full bg-black border border-neutral-800 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs text-neutral-500 uppercase mb-2">Priority</label>
                    <select name="priority" class="w-full bg-black border border-neutral-800 rounded-lg p-3 text-sm text-white focus:outline-none focus:border-white">
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="normal" selected>Normal</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2.5 bg-white text-black rounded-full font-semibold hover:bg-neutral-200">Add to War Room</button>
            </form>
        </div>

        <div class="lg:col-span-2 border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <h2 class="text-lg text-white font-bold mb-4">Active Tasks</h2>
            <div class="space-y-4">
                <?php foreach ($notes as $note):
                    $pclass = $priority_colors[$note['priority']] ?? $priority_colors['normal'];
                ?>
                    <div class="border border-neutral-800 rounded-xl p-4">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-2">
                            <div>
                                <div class="text-white text-sm leading-relaxed"><?= htmlspecialchars($note['note']) ?></div>
                                <div class="text-xs text-neutral-500 mt-2">Created: <?= htmlspecialchars($note['created_at']) ?></div>
                            </div>
                            <span class="text-[10px] px-2 py-1 rounded border <?= $pclass ?>"><?= strtoupper($note['priority']) ?></span>
                        </div>

                        <form method="POST" class="flex items-center gap-2 mt-3">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="note_id" value="<?= (int) $note['id'] ?>">
                            <select name="status" class="bg-black border border-neutral-800 rounded-lg px-3 py-2 text-xs text-white">
                                <option value="open" <?= $note['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                                <option value="in_progress" <?= $note['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="done" <?= $note['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                            </select>
                            <button type="submit" class="px-3 py-2 text-xs border border-neutral-700 rounded-lg text-neutral-300 hover:text-white hover:border-neutral-500">Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($notes)): ?>
                    <div class="text-sm text-neutral-500">No tasks yet. Add your first investigation objective.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

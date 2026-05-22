# AZNetherPortal

A lightweight, robust, and highly-optimized Nether Portal plugin for PocketMine-MP 5 designed to mimic vanilla Bedrock/Java behavior while maintaining absolute server performance and safety.

## Features

- **Bidirectional Portal Linking**: Seamlessly links portal frames in the Overworld to portal frames in the Nether. Teleporting through any block of a registered portal brings you back to the exact location of the portal on the other side.
- **Lobby Fallback**: Teleporting through an unlinked Nether portal automatically returns the player safely to the configured fallback lobby world.
- **Asynchronous Chunk Generation**: Asynchronously pre-generates a 3x3 grid of chunks in the destination world before placing portal blocks or teleporting the player to ensure safety and stability.
- **Asynchronous Database**: All database reads and writes are processed in background threads via SQLite and AsyncTasks to prevent main-thread lag spikes.
- **Thread-Safe Architecture**: Employs thread-safe serialization for task parameters, ensuring seamless asynchronous execution in PM5's multi-threaded environment.
- **3x3x3 Portal Scan**: Robust 3D scanning algorithms automatically detect complete portal structures to register them accurately in the database, even if a player steps in slightly off-center.
- **Delay & Cooldown Settings**: Highly customizable delays (seconds to stand in portal) and cooldowns (preventing teleportation loops).

## Directory Structure

The plugin is structured modularly:
```
AZNetherPortal/
├── resources/
│   └── config.yml          # Plugin configuration
├── src/BeeAZ/AZNetherPortal/
│   ├── Main.php            # Core plugin class
│   ├── listener/
│   │   └── EventListener.php # Handles block breaks, placing, and ignition
│   ├── manager/
│   │   ├── PortalManager.php # Handles database reads and local cache
│   │   └── PortalTeleporter.php # Handles teleport tasks and portal verification
│   └── task/
│       └── PortalSaveTask.php # Async database writer task
└── plugin.yml              # PocketMine-MP plugin metadata
```

## Configuration

The `config.yml` contains easy-to-use controls:

```yaml
# Nether world folder name
nether_world: "nether"

# Fallback world folder name when returning from Nether without a link
fallback_world: "lobby"

# Minimum and maximum coordinates for random teleportation in the Nether
rtp_min_x: -1000
rtp_max_x: 1000
rtp_min_z: -1000
rtp_max_z: 1000

# Teleportation delay in seconds (time players must stand in the portal)
teleport_delay: 3

# Cooldown time in seconds between teleportations to prevent infinite teleport loops
cooldown_time: 5
```

## How It Works

1. **Ignition**: When a player lights an Obsidian frame using flint and steel, the portal frame is populated with `NetherPortal` blocks.
2. **Teleportation**: Standing inside the portal triggers a countdown timer (based on `teleport_delay`).
3. **Chunk Prep**: When the timer finishes, the plugin pre-loads and pre-generates the destination chunks in the background.
4. **Portal Matching & Generation**: If the portal already exists in the database, the player is teleported. If not, the plugin searches for safe coordinates, builds a destination portal frame, registers both portals in the database, and teleports the player safely.
5. **Database Sync**: Whenever a portal block is broken or exploded, the portal is immediately removed from both the local cache and the database asynchronously.

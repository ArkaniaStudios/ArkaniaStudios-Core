<?php

declare(strict_types=1);

/**
 *     _      ____    _  __     _      _   _   ___      _
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\
 *
 * @author: Julien
 * @link: https://github.com/ArkaniaStudios
 *
 * Tous ce qui est développé par nos équipes, ou qui concerne le serveur, restent confidentiels et est interdit à l’utilisation tiers.
 */

namespace arkania\factions\claims;

use arkania\utils\Query;
use arkania\utils\trait\Provider;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use pocketmine\world\World;

class ClaimManager {

    /** @var Claim[] */
    private array $claim = [];

    use SingletonTrait;
    use Provider;

    public function __construct() {
        self::setInstance($this);

        $db = $this->getProvider()->query("SELECT * FROM claims");
        foreach($db->fetch_all() as $val)
            $this->claim[$val['chunkX'] . ':' . $val['chunkZ'] . ':' . $val['world']] = new Claim($val['factionName'], $val['chunkX'], $val['chunkZ'], $val['world']);
        $db->close();
    }

    /**
     * @param int $chunkX
     * @param int $chunkZ
     * @param string $world
     * @return Claim|null
     */
    public function getClaim(int $chunkX, int $chunkZ, string $world): ?Claim{
        return $this->claim[$chunkX . ':' . $chunkZ . ':' . $world] ?? null;
    }

    /**
     * @param Position $position
     * @return Claim|null
     */
    public function getClaimByPosition(Position $position): ?Claim {
        return $this->getClaim($position->getFloorX() >> 4, $position->getFloorZ() >> 4, $position->getWorld()->getFolderName());
    }

    /**
     * @param string $faction
     * @return array
     */
    public function getFactionClaim(string $faction): array {
        return array_filter($this->claim, function (Claim $claim) use ($faction): bool {
            return $claim->getFaction() === $faction;
        });
    }

    /**
     * @param string $faction
     * @param World $world
     * @param int $chunkX
     * @param int $chunkZ
     * @return Claim
     */
    public function createClaim(string $faction, World $world, int $chunkX, int $chunkZ): Claim {
        $args = [
            'faction' => $faction,
            'chunkX' => $chunkX,
            'chunkZ' => $chunkZ,
            'world' => $world->getFolderName()
        ];
        $this->claim[$args['chunkX'] . ':' . $args['chunkZ'] . ':' . $args['world']] = new Claim($args['faction'], $args['chunkX'], $args['chunkZ'], $args['world']);
        Query::query("INSERT INTO claims(factionName, chunkX, chunkZ, world) VALUES ('" . $args['faction'] . "', '" . $args['chunkX'] . "', '" . $args['chunkZ'] . "', '" . $args['world'] . "')");
        return $this->claim[$args['chunkX'] . ':' . $args['chunkZ'] . ':' . $args['world']];
    }

    /**
     * @param Claim $claim
     * @return void
     */
    public function deleteClaim(Claim $claim): void {
        unset($this->claim[($chunkX = $claim->getChunkX()) . ":" . ($chunkZ = $claim->getChunkZ()) . ":" . ($world = $claim->getLevel()->getFolderName())]);
        Query::query("DELETE FROM claims WHERE chunkX='" . $chunkX . "' AND chunkZ='" . $chunkZ . "' AND world='" . $world . "'");
    }

}
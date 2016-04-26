<?php
namespace ultrafactions\manager;

use pocketmine\utils\Config;
use ultrafactions\Faction;
use ultrafactions\UltraFactions;

class FactionManager
{

    /** @var Faction[] $factions */
    private $factions = [];

    public function __construct(UltraFactions $plugin)
    {
        $this->plugin = $plugin;

        $factions = (new Config($plugin->getDataFolder() . "factions.yml", Config::YAML))->getAll();
        if (!empty($factions)){
            $this->loadFactions($factions);
            $this->getPlugin()->getLogger()->info("Loaded factions.");
        } else {
            $plugin->getLogger()->info("No factions to load.");
        }
    }

    /**
     * @param array $factions
     * @return null|Faction
     */
    private function loadFactions(array $factions)
    {
        foreach ($factions as $i => $name) {
            $faction = $this->getPlugin()->getDataProvider()->getFactionData($name);
            if($this->loadFaction($name, $faction) instanceof Faction){
                $this->getPlugin()->getLogger()->debug("Loaded faction '$name'");
            }
        }
    }

    public function getPlugin() : UltraFactions
    {
        return $this->plugin;
    }

    /**
     * @param $name
     * @param array $faction
     * @return null|Faction
     */
    private function loadFaction($name, array $faction)
    {

            $this->factions[strtolower($name)] = new Faction($name, $faction);
            return $this->getFaction($name);
        return null;
    }

    /**
     * @param $name
     * @return null|Faction
     */
    public function getFaction($name)
    {
        if (isset($this->factions[strtolower($name)])) {
            return $this->factions[strtolower($name)];
        }
        return null;
    }

    /**
     * @param Faction $faction
     */
    public function addFaction(Faction $faction)
    {
        $this->factions[strtolower($faction->getName())] = $faction;
    }

    /**
     * @param Faction $faction
     */
    public function deleteFaction(Faction $faction)
    {
    }

    /**
     * @param Faction $faction
     */
    public function saveFaction(Faction $faction)
    {
    }

    public function close()
    {
        $this->save();
        $this->plugin->getLogger()->debug("Closed FactionManager.");
    }

    /**
     * Saves all loaded factions into faction.yml
     * And each faction class
     */
    public function save()
    {
        $l = $this->getPlugin()->getLogger();
        $factions = [];
        foreach ($this->factions as $name => $f) {
            $f->save();
            $this->getPlugin()->getLogger()->debug("Saved faction's '{$f->getName()}' data.");
            $factions[] = $name;
        }
        @unlink($this->getPlugin()->getDataFolder() . "factions.yml");
        new Config($this->getPlugin()->getDataFolder() . "factions.yml", Config::YAML, $factions);
        $this->getPlugin()->getLogger()->info("Saved factions data.");
    }
}
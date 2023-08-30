<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final since Symfony 3.3
 */
class TrustedshopseasyintegrationFrontContainer extends Container
{
    private $parameters = [];
    private $targetDirs = [];

    public function __construct()
    {
        $this->services = [];
        $this->normalizedIds = [
            'trustedshopsaddon\\api\\logger\\apilogger' => 'TrustedshopsAddon\\API\\Logger\\ApiLogger',
            'trustedshopsaddon\\repository\\channelrepository' => 'TrustedshopsAddon\\Repository\\ChannelRepository',
            'trustedshopsaddon\\repository\\orderproductrepository' => 'TrustedshopsAddon\\Repository\\OrderProductRepository',
            'trustedshopsaddon\\service\\channelservice' => 'TrustedshopsAddon\\Service\\ChannelService',
            'trustedshopsaddon\\service\\credentialsservice' => 'TrustedshopsAddon\\Service\\CredentialsService',
            'trustedshopsaddon\\service\\hookservice' => 'TrustedshopsAddon\\Service\\HookService',
            'trustedshopsaddon\\service\\orderproductservice' => 'TrustedshopsAddon\\Service\\OrderProductService',
            'trustedshopsaddon\\service\\orderstatusservice' => 'TrustedshopsAddon\\Service\\OrderStatusService',
        ];
        $this->methodMap = [
            'TrustedshopsAddon\\API\\Logger\\ApiLogger' => 'getApiLoggerService',
            'TrustedshopsAddon\\Repository\\ChannelRepository' => 'getChannelRepositoryService',
            'TrustedshopsAddon\\Repository\\OrderProductRepository' => 'getOrderProductRepositoryService',
            'TrustedshopsAddon\\Service\\ChannelService' => 'getChannelServiceService',
            'TrustedshopsAddon\\Service\\CredentialsService' => 'getCredentialsServiceService',
            'TrustedshopsAddon\\Service\\HookService' => 'getHookServiceService',
            'TrustedshopsAddon\\Service\\OrderProductService' => 'getOrderProductServiceService',
            'TrustedshopsAddon\\Service\\OrderStatusService' => 'getOrderStatusServiceService',
            'trustedshopseasyintegration' => 'getTrustedshopseasyintegrationService',
        ];
        $this->privates = [
            'trustedshopseasyintegration' => true,
        ];

        $this->aliases = [];
    }

    public function getRemovedIds()
    {
        return [
            'Psr\\Container\\ContainerInterface' => true,
            'Symfony\\Component\\DependencyInjection\\ContainerInterface' => true,
            'trustedshopseasyintegration' => true,
        ];
    }

    public function compile()
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }

    public function isCompiled()
    {
        return true;
    }

    public function isFrozen()
    {
        @trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the isCompiled() method instead.', __METHOD__), E_USER_DEPRECATED);

        return true;
    }

    /**
     * Gets the public 'TrustedshopsAddon\API\Logger\ApiLogger' shared service.
     *
     * @return \TrustedshopsAddon\API\Logger\ApiLogger
     */
    protected function getApiLoggerService()
    {
        return $this->services['TrustedshopsAddon\\API\\Logger\\ApiLogger'] = new \TrustedshopsAddon\API\Logger\ApiLogger();
    }

    /**
     * Gets the public 'TrustedshopsAddon\Repository\ChannelRepository' shared service.
     *
     * @return \TrustedshopsAddon\Repository\ChannelRepository
     */
    protected function getChannelRepositoryService()
    {
        return $this->services['TrustedshopsAddon\\Repository\\ChannelRepository'] = new \TrustedshopsAddon\Repository\ChannelRepository();
    }

    /**
     * Gets the public 'TrustedshopsAddon\Repository\OrderProductRepository' shared service.
     *
     * @return \TrustedshopsAddon\Repository\OrderProductRepository
     */
    protected function getOrderProductRepositoryService()
    {
        return $this->services['TrustedshopsAddon\\Repository\\OrderProductRepository'] = new \TrustedshopsAddon\Repository\OrderProductRepository();
    }

    /**
     * Gets the public 'TrustedshopsAddon\Service\ChannelService' shared service.
     *
     * @return \TrustedshopsAddon\Service\ChannelService
     */
    protected function getChannelServiceService()
    {
        return $this->services['TrustedshopsAddon\\Service\\ChannelService'] = new \TrustedshopsAddon\Service\ChannelService(${($_ = isset($this->services['TrustedshopsAddon\\Repository\\ChannelRepository']) ? $this->services['TrustedshopsAddon\\Repository\\ChannelRepository'] : ($this->services['TrustedshopsAddon\\Repository\\ChannelRepository'] = new \TrustedshopsAddon\Repository\ChannelRepository())) && false ?: '_'}, ${($_ = isset($this->services['TrustedshopsAddon\\Service\\CredentialsService']) ? $this->services['TrustedshopsAddon\\Service\\CredentialsService'] : ($this->services['TrustedshopsAddon\\Service\\CredentialsService'] = new \TrustedshopsAddon\Service\CredentialsService())) && false ?: '_'}, ${($_ = isset($this->services['TrustedshopsAddon\\Service\\OrderProductService']) ? $this->services['TrustedshopsAddon\\Service\\OrderProductService'] : $this->getOrderProductServiceService()) && false ?: '_'});
    }

    /**
     * Gets the public 'TrustedshopsAddon\Service\CredentialsService' shared service.
     *
     * @return \TrustedshopsAddon\Service\CredentialsService
     */
    protected function getCredentialsServiceService()
    {
        return $this->services['TrustedshopsAddon\\Service\\CredentialsService'] = new \TrustedshopsAddon\Service\CredentialsService();
    }

    /**
     * Gets the public 'TrustedshopsAddon\Service\HookService' shared service.
     *
     * @return \TrustedshopsAddon\Service\HookService
     */
    protected function getHookServiceService()
    {
        return $this->services['TrustedshopsAddon\\Service\\HookService'] = new \TrustedshopsAddon\Service\HookService(${($_ = isset($this->services['TrustedshopsAddon\\Service\\ChannelService']) ? $this->services['TrustedshopsAddon\\Service\\ChannelService'] : $this->getChannelServiceService()) && false ?: '_'}, ${($_ = isset($this->services['TrustedshopsAddon\\Service\\OrderProductService']) ? $this->services['TrustedshopsAddon\\Service\\OrderProductService'] : $this->getOrderProductServiceService()) && false ?: '_'});
    }

    /**
     * Gets the public 'TrustedshopsAddon\Service\OrderProductService' shared service.
     *
     * @return \TrustedshopsAddon\Service\OrderProductService
     */
    protected function getOrderProductServiceService()
    {
        return $this->services['TrustedshopsAddon\\Service\\OrderProductService'] = new \TrustedshopsAddon\Service\OrderProductService(${($_ = isset($this->services['TrustedshopsAddon\\Repository\\OrderProductRepository']) ? $this->services['TrustedshopsAddon\\Repository\\OrderProductRepository'] : ($this->services['TrustedshopsAddon\\Repository\\OrderProductRepository'] = new \TrustedshopsAddon\Repository\OrderProductRepository())) && false ?: '_'});
    }

    /**
     * Gets the public 'TrustedshopsAddon\Service\OrderStatusService' shared service.
     *
     * @return \TrustedshopsAddon\Service\OrderStatusService
     */
    protected function getOrderStatusServiceService()
    {
        return $this->services['TrustedshopsAddon\\Service\\OrderStatusService'] = new \TrustedshopsAddon\Service\OrderStatusService(${($_ = isset($this->services['TrustedshopsAddon\\Service\\ChannelService']) ? $this->services['TrustedshopsAddon\\Service\\ChannelService'] : $this->getChannelServiceService()) && false ?: '_'}, ${($_ = isset($this->services['TrustedshopsAddon\\Service\\CredentialsService']) ? $this->services['TrustedshopsAddon\\Service\\CredentialsService'] : ($this->services['TrustedshopsAddon\\Service\\CredentialsService'] = new \TrustedshopsAddon\Service\CredentialsService())) && false ?: '_'});
    }

    /**
     * Gets the private 'trustedshopseasyintegration' shared service.
     *
     * @return \Trustedshopseasyintegration
     */
    protected function getTrustedshopseasyintegrationService()
    {
        return $this->services['trustedshopseasyintegration'] = \Module::getInstanceByName('trustedshopseasyintegration');
    }
}

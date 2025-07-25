<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Service\UrlBuilder\FilesystemUrl;

use SprykerDemo\Service\UrlBuilder\Exception\UrlBuilderException;
use SprykerDemo\Service\UrlBuilder\FilesystemUrl\Configuration\FileStorageConfigurationFactoryInterface;
use SprykerDemo\Service\UrlBuilder\FilesystemUrl\Configuration\FileStorageConfigurationInterface;
use SprykerDemo\Service\UrlBuilder\FilesystemUrl\FileStorageUrl\FileStorageUrlBuilderInterface;

class FilesystemUrlBuilder implements FilesystemUrlBuilderInterface
{
    /**
     * @var array<\SprykerDemo\Service\UrlBuilder\FilesystemUrl\FileStorageUrl\FileStorageUrlBuilderInterface>
     */
    protected array $fileUrlBuilders;

    /**
     * @var \SprykerDemo\Service\UrlBuilder\FilesystemUrl\Configuration\FileStorageConfigurationFactoryInterface
     */
    protected FileStorageConfigurationFactoryInterface $fileStorageConfigurationFactory;

    /**
     * @param array<\SprykerDemo\Service\UrlBuilder\FilesystemUrl\FileStorageUrl\FileStorageUrlBuilderInterface> $fileUrlBuilders
     * @param \SprykerDemo\Service\UrlBuilder\FilesystemUrl\Configuration\FileStorageConfigurationFactoryInterface $fileStorageConfigurationFactory
     */
    public function __construct(array $fileUrlBuilders, FileStorageConfigurationFactoryInterface $fileStorageConfigurationFactory)
    {
        $this->fileUrlBuilders = $fileUrlBuilders;
        $this->fileStorageConfigurationFactory = $fileStorageConfigurationFactory;
    }

    /**
     * @param string $filePath
     * @param string $filesystemName
     *
     * @return string
     */
    public function buildFileUrl(string $filePath, string $filesystemName): string
    {
        $fileStorageConfiguration = $this->fileStorageConfigurationFactory->createFileStorageConfiguration($filesystemName);
        $fileStorageUrlBuilder = $this->getFileStorageUrlBuilder($fileStorageConfiguration);

        return $fileStorageUrlBuilder->buildFileUrl($filePath, $fileStorageConfiguration);
    }

    /**
     * @param \SprykerDemo\Service\UrlBuilder\FilesystemUrl\Configuration\FileStorageConfigurationInterface $fileStorageConfiguration
     *
     * @throws \SprykerDemo\Service\UrlBuilder\Exception\UrlBuilderException
     *
     * @return \SprykerDemo\Service\UrlBuilder\FilesystemUrl\FileStorageUrl\FileStorageUrlBuilderInterface
     */
    protected function getFileStorageUrlBuilder(FileStorageConfigurationInterface $fileStorageConfiguration): FileStorageUrlBuilderInterface
    {
        foreach ($this->fileUrlBuilders as $fileUrlBuilder) {
            if ($fileUrlBuilder->isApplicable($fileStorageConfiguration->getAdapterClassName())) {
                return $fileUrlBuilder;
            }
        }

        throw new UrlBuilderException(
            sprintf(
                'Url builder is not configured for "%s" adapter class. ',
                $fileStorageConfiguration->getAdapterClassName(),
            ),
        );
    }
}

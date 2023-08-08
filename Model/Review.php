<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model;

use CustomerFeel\ApiConnector\Api\Data\ReviewInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Review implements ReviewInterface, \Serializable
{
    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    private string $content;
    private ?int $sentiment = null;
    private string $author;
    private ?string $externalId;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getSentiment(): ?int
    {
        return $this->sentiment;
    }

    public function setSentiment(?int $sentiment): self
    {
        $this->sentiment = $sentiment;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function serialize(): string
    {
        return $this->serializer->serialize([
            'content' => $this->content,
            'sentiment' => $this->sentiment,
            'author' => $this->author,
            'externalId' => $this->externalId,
        ]);
    }

    public function unserialize(string $data): void
    {
        $unSerializedData = $this->serializer->unserialize($data);

        $this->content = $unSerializedData['content'];
        $this->sentiment = $unSerializedData['sentiment'];
        $this->author = $unSerializedData['author'];
        $this->externalId = $unSerializedData['externalId'];

    }

    public function __serialize(): array
    {
        return [
            'content' => $this->content,
            'sentiment' => $this->sentiment,
            'author' => $this->author,
            'externalId' => $this->externalId,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->setContent($data['content'])
            ->setSentiment($data['sentiment'])
            ->setAuthor($data['author'])
            ->setExternalId($data['externalId']);
    }
}

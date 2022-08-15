<?php

namespace App\Factory;

use App\Entity\Blog\Post;
use App\Repository\Blog\PostRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Symfony\Component\String\Slugger\AsciiSlugger;

use App\Factory\UserFactory;

/**
 * @extends ModelFactory<Post>
 *
 * @method static Post|Proxy createOne(array $attributes = [])
 * @method static Post[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Post|Proxy find(object|array|mixed $criteria)
 * @method static Post|Proxy findOrCreate(array $attributes)
 * @method static Post|Proxy first(string $sortedField = 'id')
 * @method static Post|Proxy last(string $sortedField = 'id')
 * @method static Post|Proxy random(array $attributes = [])
 * @method static Post|Proxy randomOrCreate(array $attributes = [])
 * @method static Post[]|Proxy[] all()
 * @method static Post[]|Proxy[] findBy(array $attributes)
 * @method static Post[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Post[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method Post|Proxy create(array|callable $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->realText(150),
            'isPublished' => self::faker()->boolean(85),
            'author' => UserFactory::random([
                'isVerified' => true,
            ]),
            'content' => array_reduce(
                self::faker()->paragraphs(
                    self::faker()->numberBetween(3, 15),
                    false
                ),
                function($content, $paragraph) {
                    $content .= '<p>' .$paragraph . '</p>' . "\n";
                    return $content;
                },
            ),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-100 days', '-1 days')),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function(Post $post) {
                if (!$post->getSlug()) {
                    $slugger = new AsciiSlugger();
                    $slug = $post->getCreatedAt()->format('d.m.Y').'-'.strtolower($slugger->slug($post->getTitle()));
                    $post->setSlug($slug);
                }
                
                if (!$post->getUpdatedAt() && self::faker()->boolean(85)) {
                    $post->setUpdatedAt(
                        \DateTimeImmutable::createFromMutable(
                            self::faker()->dateTimeBetween($post->getCreatedAt()->format(\DateTime::ISO8601), '-1 days')
                        )
                    );
                }
            })
        ;
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}

<?php

/**
 * A helper file for Kirby, to provide autocomplete information to your IDE.
 * This file was automatically generated with the Kirby Types plugin.
 *
 * @see https://github.com/lukaskleinschmidt/kirby-types
 */

namespace Kirby\Cms
{
    class Site
    {
        /**
         * Returns the generalHeadline field.
         *
         * Uses a `headline` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function generalHeadline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->generalHeadline();
        }
        /**
         * Returns the description field.
         *
         * Uses a `textarea` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function description(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->description();
        }
        /**
         * Returns the tags field.
         *
         * Uses a `tags` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/tags
         */
        public function tags(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->tags();
        }
        /**
         * Returns the footerHeadline field.
         *
         * Uses a `headline` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function footerHeadline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->footerHeadline();
        }
        /**
         * Returns the footerSponsorsHint field.
         *
         * Uses a `text` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function footerSponsorsHint(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->footerSponsorsHint();
        }
        /**
         * Returns the footerAboutUs field.
         *
         * Uses a `textarea` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function footerAboutUs(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->footerAboutUs();
        }
        /**
         * Returns the footerContactLabel field.
         *
         * Uses a `text` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function footerContactLabel(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->footerContactLabel();
        }
        /**
         * Returns the footerContactHint field.
         *
         * Uses a `textarea` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function footerContactHint(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->footerContactHint();
        }
        /**
         * Returns the socialHeadline field.
         *
         * Uses a `headline` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function socialHeadline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->socialHeadline();
        }
        /**
         * Returns the socialLinks field.
         *
         * Uses a `structure` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function socialLinks(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->socialLinks();
        }
        /**
         * Returns the recipient field.
         *
         * Uses a `email` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/email
         */
        public function recipient(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->recipient();
        }
        /**
         * Returns the sender field.
         *
         * Uses a `email` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/email
         */
        public function sender(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->sender();
        }
        /**
         * Returns the subject field.
         *
         * Uses a `text` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function subject(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->subject();
        }
        /**
         * Returns the messageSentText field.
         *
         * Uses a `textarea` field in the `site` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function messageSentText(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->messageSentText();
        }
        public function numberOfPendingComments()
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->numberOfPendingComments();
        }
        public function numberOfSpamComments()
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->numberOfSpamComments();
        }
        public function latestComments($limit = 5, $showWebmentions = false)
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->latestComments($limit, $showWebmentions);
        }
        public function latestCommentsPerPage($limit = 5, $showWebmentions = false)
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->latestCommentsPerPage($limit, $showWebmentions);
        }
        public function getAppleMetadata($endpoint = 'categories')
        {
            /** @var \Kirby\Cms\Site $instance */
            return $instance->getAppleMetadata($endpoint);
        }
    }
    class StructureObject
    {
        /**
         * Returns the network field.
         *
         * Uses a `select` field in the `site.socialLinks` blueprint.\
         * Uses a `select` field in the `pages/participant.external_profiles` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function network(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->network();
        }
        /**
         * Returns the url field.
         *
         * Uses a `url` field in the `site.socialLinks` blueprint.\
         * Uses a `url` field in the `pages/participant.external_profiles` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function url(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->url();
        }
        /**
         * Returns the podcasterTranscriptLanguage field.
         *
         * Uses a `select` field in the `pages/episode.podcasterTranscript` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function podcasterTranscriptLanguage(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterTranscriptLanguage();
        }
        /**
         * Returns the podcasterTranscriptFile field.
         *
         * Uses a `files` field in the `pages/episode.podcasterTranscript` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function podcasterTranscriptFile(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterTranscriptFile();
        }
        /**
         * Returns the podcasterChapterTimestamp field.
         *
         * Uses a `text` field in the `pages/episode.podcasterChapters` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterChapterTimestamp(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterChapterTimestamp();
        }
        /**
         * Returns the podcasterChapterTitle field.
         *
         * Uses a `text` field in the `pages/episode.podcasterChapters` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterChapterTitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterChapterTitle();
        }
        /**
         * Returns the podcasterChapterUrl field.
         *
         * Uses a `url` field in the `pages/episode.podcasterChapters` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function podcasterChapterUrl(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterChapterUrl();
        }
        /**
         * Returns the podcasterChapterImage field.
         *
         * Uses a `files` field in the `pages/episode.podcasterChapters` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function podcasterChapterImage(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterChapterImage();
        }
        /**
         * Returns the profile_label field.
         *
         * Uses a `text` field in the `pages/participant.external_profiles` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function profile_label(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->profile_label();
        }
        /**
         * Returns the podcasterMainCategory field.
         *
         * Uses a `select` field in the `pages/podcasterfeed.podcasterCategories` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function podcasterMainCategory(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->podcasterMainCategory();
        }
        /**
         * Returns the colorType field.
         *
         * Uses a `select` field in the `pages/podcasterfeed.podcasterPodloveColors` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function colorType(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->colorType();
        }
        /**
         * Returns the hex field.
         *
         * Uses a `text` field in the `pages/podcasterfeed.podcasterPodloveColors` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function hex(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->hex();
        }
        /**
         * Returns the fontType field.
         *
         * Uses a `select` field in the `pages/podcasterfeed.podcasterPodloveFonts` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function fontType(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->fontType();
        }
        /**
         * Returns the name field.
         *
         * Uses a `text` field in the `pages/podcasterfeed.podcasterPodloveFonts` blueprint.\
         * Uses a `text` field in the `pages/team.members` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function name(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->name();
        }
        /**
         * Returns the family field.
         *
         * Uses a `tags` field in the `pages/podcasterfeed.podcasterPodloveFonts` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/tags
         */
        public function family(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->family();
        }
        /**
         * Returns the weight field.
         *
         * Uses a `number` field in the `pages/podcasterfeed.podcasterPodloveFonts` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function weight(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->weight();
        }
        /**
         * Returns the src field.
         *
         * Uses a `tags` field in the `pages/podcasterfeed.podcasterPodloveFonts` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/tags
         */
        public function src(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->src();
        }
        /**
         * Returns the client field.
         *
         * Uses a `select` field in the `pages/podcasterfeed.podcasterPodloveClients` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function client(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->client();
        }
        /**
         * Returns the service field.
         *
         * Uses a `text` field in the `pages/podcasterfeed.podcasterPodloveClients` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function service(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->service();
        }
        /**
         * Returns the roleId field.
         *
         * Uses a `number` field in the `pages/podcasterfeed.podcasterPodloveRoles` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function roleId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->roleId();
        }
        /**
         * Returns the roleTitle field.
         *
         * Uses a `text` field in the `pages/podcasterfeed.podcasterPodloveRoles` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function roleTitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->roleTitle();
        }
        /**
         * Returns the groupId field.
         *
         * Uses a `number` field in the `pages/podcasterfeed.podcasterPodloveGroups` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function groupId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->groupId();
        }
        /**
         * Returns the groupTitle field.
         *
         * Uses a `text` field in the `pages/podcasterfeed.podcasterPodloveGroups` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function groupTitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->groupTitle();
        }
        /**
         * Returns the role field.
         *
         * Uses a `text` field in the `pages/team.members` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function role(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->role();
        }
        /**
         * Returns the bio field.
         *
         * Uses a `textarea` field in the `pages/team.members` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function bio(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\StructureObject $instance */
            return $instance->bio();
        }
    }
    class Page
    {
        /**
         * Returns the podcasterAudio field.
         *
         * Uses a `files` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function podcasterAudio(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterAudio();
        }
        /**
         * Returns the podcasterTranscript field.
         *
         * Uses a `structure` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterTranscript(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterTranscript();
        }
        /**
         * Returns the podcasterCover field.
         *
         * Uses a `files` field in the `pages/episode` blueprint.\
         * Uses a `files` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function podcasterCover(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterCover();
        }
        /**
         * Returns the podcasterEmpty field.
         *
         * Uses a `hidden` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/hidden
         */
        public function podcasterEmpty(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEmpty();
        }
        /**
         * Returns the podcasterEmpty2 field.
         *
         * Uses a `hidden` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/hidden
         */
        public function podcasterEmpty2(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEmpty2();
        }
        /**
         * Returns the headlineSeasonEpisode field.
         *
         * Uses a `headline` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineSeasonEpisode(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineSeasonEpisode();
        }
        /**
         * Returns the podcasterSeason field.
         *
         * Uses a `number` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function podcasterSeason(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterSeason();
        }
        /**
         * Returns the podcasterEpisode field.
         *
         * Uses a `number` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function podcasterEpisode(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEpisode();
        }
        /**
         * Returns the podcasterEpisodeTotal field.
         *
         * Uses a `number` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function podcasterEpisodeTotal(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEpisodeTotal();
        }
        /**
         * Returns the podcasterEpisodeType field.
         *
         * Uses a `select` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function podcasterEpisodeType(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEpisodeType();
        }
        /**
         * Returns the podcasterEpisodeTypeTrailerInfo field.
         *
         * Uses a `info` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function podcasterEpisodeTypeTrailerInfo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEpisodeTypeTrailerInfo();
        }
        /**
         * Returns the podcasterEpisodeTypeBonusInfo field.
         *
         * Uses a `info` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function podcasterEpisodeTypeBonusInfo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterEpisodeTypeBonusInfo();
        }
        /**
         * Returns the line2 field.
         *
         * Uses a `line` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/line
         */
        public function line2(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->line2();
        }
        /**
         * Returns the headlineEpisodeDetails field.
         *
         * Uses a `headline` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineEpisodeDetails(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineEpisodeDetails();
        }
        /**
         * Returns the podcasterTitle field.
         *
         * Uses a `text` field in the `pages/episode` blueprint.\
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterTitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterTitle();
        }
        /**
         * Returns the podcasterSubtitle field.
         *
         * Uses a `text` field in the `pages/episode` blueprint.\
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterSubtitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterSubtitle();
        }
        /**
         * Returns the podcasterDescription field.
         *
         * Uses a `markdown` field in the `pages/episode` blueprint.\
         * Uses a `textarea` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function podcasterDescription(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterDescription();
        }
        /**
         * Returns the podcasterChapters field.
         *
         * Uses a `structure` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterChapters(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterChapters();
        }
        /**
         * Returns the headlineContributors field.
         *
         * Uses a `headline` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineContributors(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineContributors();
        }
        /**
         * Returns the podcasterAuthor field.
         *
         * Uses a `users` field in the `pages/episode` blueprint.\
         * Uses a `users` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/users
         */
        public function podcasterAuthor(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterAuthor();
        }
        /**
         * Returns the podcasterHosts field.
         *
         * Uses a `pages` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function podcasterHosts(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterHosts();
        }
        /**
         * Returns the podcasterGuests field.
         *
         * Uses a `pages` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function podcasterGuests(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterGuests();
        }
        /**
         * Returns the line field.
         *
         * Uses a `line` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/line
         */
        public function line(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->line();
        }
        /**
         * Returns the headlineBreakingchanges field.
         *
         * Uses a `headline` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineBreakingchanges(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineBreakingchanges();
        }
        /**
         * Returns the podcasterExplicit field.
         *
         * Uses a `toggle` field in the `pages/episode` blueprint.\
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterExplicit(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterExplicit();
        }
        /**
         * Returns the podcasterBlock field.
         *
         * Uses a `toggle` field in the `pages/episode` blueprint.\
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterBlock(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterBlock();
        }
        /**
         * Returns the date field.
         *
         * Uses a `date` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/date
         */
        public function date(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->date();
        }
        /**
         * Returns the rerelease field.
         *
         * Uses a `date` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/date
         */
        public function rerelease(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->rerelease();
        }
        /**
         * Returns the text field.
         *
         * Uses a `markdown` field in the `pages/episode` blueprint.\
         * Uses a `blocks` field in the `pages/home` blueprint.\
         * Uses a `blocks` field in the `pages/page` blueprint.\
         * Uses a `blocks` field in the `pages/team` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/blocks
         */
        public function text(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->text();
        }
        /**
         * Returns the kommentsEnabledOnpage field.
         *
         * Uses a `toggle` field in the `pages/episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function kommentsEnabledOnpage(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->kommentsEnabledOnpage();
        }
        /**
         * Returns the kommentsInbox field.
         *
         * Uses a `CommentsTable` field in the `pages/episode` blueprint.
         */
        public function kommentsInbox(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->kommentsInbox();
        }
        /**
         * Returns the form_fields field.
         *
         * Uses a `layout` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/layout
         */
        public function form_fields(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->form_fields();
        }
        /**
         * Returns the save_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function save_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->save_headline();
        }
        /**
         * Returns the save_entries field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function save_entries(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->save_entries();
        }
        /**
         * Returns the save_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function save_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->save_gap();
        }
        /**
         * Returns the submit_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function submit_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->submit_headline();
        }
        /**
         * Returns the label_submit field.
         *
         * Uses a `text` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function label_submit(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->label_submit();
        }
        /**
         * Returns the submit_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function submit_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->submit_gap();
        }
        /**
         * Returns the success_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function success_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->success_headline();
        }
        /**
         * Returns the success_type field.
         *
         * Uses a `toggles` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggles
         */
        public function success_type(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->success_type();
        }
        /**
         * Returns the success_text field.
         *
         * Uses a `writer` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/writer
         */
        public function success_text(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->success_text();
        }
        /**
         * Returns the success_page field.
         *
         * Uses a `pages` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function success_page(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->success_page();
        }
        /**
         * Returns the success_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function success_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->success_gap();
        }
        /**
         * Returns the error_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function error_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->error_headline();
        }
        /**
         * Returns the error_invalidFields field.
         *
         * Uses a `writer` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/writer
         */
        public function error_invalidFields(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->error_invalidFields();
        }
        /**
         * Returns the error_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function error_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->error_gap();
        }
        /**
         * Returns the confirmationEmail_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function confirmationEmail_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_headline();
        }
        /**
         * Returns the confirmationEmail_enabled field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function confirmationEmail_enabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_enabled();
        }
        /**
         * Returns the confirmationEmail_to field.
         *
         * Uses a `form-email-select` field in the `pages/form` blueprint.
         */
        public function confirmationEmail_to(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_to();
        }
        /**
         * Returns the confirmationEmail_from field.
         *
         * Uses a `select` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function confirmationEmail_from(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_from();
        }
        /**
         * Returns the confirmationEmail_subject field.
         *
         * Uses a `text` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function confirmationEmail_subject(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_subject();
        }
        /**
         * Returns the confirmationEmail_type field.
         *
         * Uses a `toggles` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggles
         */
        public function confirmationEmail_type(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_type();
        }
        /**
         * Returns the confirmationEmail_body field.
         *
         * Uses a `textarea` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function confirmationEmail_body(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_body();
        }
        /**
         * Returns the confirmationEmail_template field.
         *
         * Uses a `select` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function confirmationEmail_template(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_template();
        }
        /**
         * Returns the confirmationEmail_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function confirmationEmail_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->confirmationEmail_gap();
        }
        /**
         * Returns the notificationEmail_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function notificationEmail_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_headline();
        }
        /**
         * Returns the notificationEmail_enabled field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function notificationEmail_enabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_enabled();
        }
        /**
         * Returns the notificationEmail_to field.
         *
         * Uses a `email` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/email
         */
        public function notificationEmail_to(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_to();
        }
        /**
         * Returns the notificationEmail_from field.
         *
         * Uses a `select` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function notificationEmail_from(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_from();
        }
        /**
         * Returns the notificationEmail_subject field.
         *
         * Uses a `text` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function notificationEmail_subject(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_subject();
        }
        /**
         * Returns the notificationEmail_type field.
         *
         * Uses a `toggles` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggles
         */
        public function notificationEmail_type(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_type();
        }
        /**
         * Returns the notificationEmail_body field.
         *
         * Uses a `textarea` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function notificationEmail_body(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_body();
        }
        /**
         * Returns the notificationEmail_template field.
         *
         * Uses a `select` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function notificationEmail_template(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_template();
        }
        /**
         * Returns the notificationEmail_gap field.
         *
         * Uses a `gap` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function notificationEmail_gap(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->notificationEmail_gap();
        }
        /**
         * Returns the brevo_headline field.
         *
         * Uses a `headline` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function brevo_headline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_headline();
        }
        /**
         * Returns the brevo_enabled field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function brevo_enabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_enabled();
        }
        /**
         * Returns the brevo_email field.
         *
         * Uses a `form-email-select` field in the `pages/form` blueprint.
         */
        public function brevo_email(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_email();
        }
        /**
         * Returns the brevo_listId field.
         *
         * Uses a `number` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function brevo_listId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_listId();
        }
        /**
         * Returns the brevo_updateEnabled field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function brevo_updateEnabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_updateEnabled();
        }
        /**
         * Returns the brevo_doubleOptIn field.
         *
         * Uses a `toggle` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function brevo_doubleOptIn(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_doubleOptIn();
        }
        /**
         * Returns the brevo_redirectionUrl field.
         *
         * Uses a `url` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function brevo_redirectionUrl(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_redirectionUrl();
        }
        /**
         * Returns the brevo_templateId field.
         *
         * Uses a `number` field in the `pages/form` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function brevo_templateId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->brevo_templateId();
        }
        /**
         * Returns the popularEpisodes field.
         *
         * Uses a `pages` field in the `pages/home` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function popularEpisodes(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->popularEpisodes();
        }
        /**
         * Returns the header field.
         *
         * Uses a `text` field in the `pages/mediathek` blueprint.\
         * Uses a `text` field in the `pages/season` blueprint.\
         * Uses a `text` field in the `pages/teilnehmende` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function header(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->header();
        }
        /**
         * Returns the lead field.
         *
         * Uses a `textarea` field in the `pages/mediathek` blueprint.\
         * Uses a `textarea` field in the `pages/participant` blueprint.\
         * Uses a `textarea` field in the `pages/season` blueprint.\
         * Uses a `textarea` field in the `pages/teilnehmende` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function lead(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->lead();
        }
        /**
         * Returns the blocks field.
         *
         * Uses a `blocks` field in the `pages/mediathek` blueprint.\
         * Uses a `blocks` field in the `pages/season` blueprint.\
         * Uses a `blocks` field in the `pages/teilnehmende` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/blocks
         */
        public function blocks(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->blocks();
        }
        /**
         * Returns the first_name field.
         *
         * Uses a `text` field in the `pages/participant` blueprint.\
         * Uses a `text` field in the `pages/testimonial` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function first_name(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->first_name();
        }
        /**
         * Returns the last_name field.
         *
         * Uses a `text` field in the `pages/participant` blueprint.\
         * Uses a `text` field in the `pages/testimonial` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function last_name(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->last_name();
        }
        /**
         * Returns the profession field.
         *
         * Uses a `text` field in the `pages/participant` blueprint.\
         * Uses a `text` field in the `pages/testimonial` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function profession(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->profession();
        }
        /**
         * Returns the description field.
         *
         * Uses a `textarea` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function description(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->description();
        }
        /**
         * Returns the external_profiles field.
         *
         * Uses a `structure` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function external_profiles(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->external_profiles();
        }
        /**
         * Returns the linked_user field.
         *
         * Uses a `users` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/users
         */
        public function linked_user(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->linked_user();
        }
        /**
         * Returns the profile_image field.
         *
         * Uses a `files` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function profile_image(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->profile_image();
        }
        /**
         * Returns the participant_role field.
         *
         * Uses a `radio` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/radio
         */
        public function participant_role(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->participant_role();
        }
        /**
         * Returns the additional_roles field.
         *
         * Uses a `checkboxes` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/checkboxes
         */
        public function additional_roles(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->additional_roles();
        }
        /**
         * Returns the gender_identities field.
         *
         * Uses a `select` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function gender_identities(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->gender_identities();
        }
        /**
         * Returns the self_described_gender field.
         *
         * Uses a `text` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function self_described_gender(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->self_described_gender();
        }
        /**
         * Returns the pronouns field.
         *
         * Uses a `text` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function pronouns(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->pronouns();
        }
        /**
         * Returns the participation_stats field.
         *
         * Uses a `info` field in the `pages/participant` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function participation_stats(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->participation_stats();
        }
        /**
         * Returns the headlineInfos field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineInfos(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineInfos();
        }
        /**
         * Returns the podcasterCopyright field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterCopyright(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterCopyright();
        }
        /**
         * Returns the podcasterKeywords field.
         *
         * Uses a `tags` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/tags
         */
        public function podcasterKeywords(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterKeywords();
        }
        /**
         * Returns the podcasterCategories field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterCategories(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterCategories();
        }
        /**
         * Returns the podcasterType field.
         *
         * Uses a `radio` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/radio
         */
        public function podcasterType(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterType();
        }
        /**
         * Returns the podcasterLanguage field.
         *
         * Uses a `select` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function podcasterLanguage(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterLanguage();
        }
        /**
         * Returns the podcasterOwner field.
         *
         * Uses a `users` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/users
         */
        public function podcasterOwner(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterOwner();
        }
        /**
         * Returns the headlineInfo field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineInfo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineInfo();
        }
        /**
         * Returns the podcastId field.
         *
         * Uses a `slug` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/slug
         */
        public function podcastId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcastId();
        }
        /**
         * Returns the podcasterLink field.
         *
         * Uses a `url` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function podcasterLink(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterLink();
        }
        /**
         * Returns the podcasterAtomLink field.
         *
         * Uses a `url` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function podcasterAtomLink(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterAtomLink();
        }
        /**
         * Returns the podcasterSource field.
         *
         * Uses a `pages` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function podcasterSource(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterSource();
        }
        /**
         * Returns the headlineDanger field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineDanger(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineDanger();
        }
        /**
         * Returns the infoDanger field.
         *
         * Uses a `info` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function infoDanger(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->infoDanger();
        }
        /**
         * Returns the podcasterComplete field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterComplete(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterComplete();
        }
        /**
         * Returns the podcasterNewFeedUrl field.
         *
         * Uses a `url` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function podcasterNewFeedUrl(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterNewFeedUrl();
        }
        /**
         * Returns the headlinePlayer field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlinePlayer(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlinePlayer();
        }
        /**
         * Returns the playerType field.
         *
         * Uses a `select` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function playerType(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->playerType();
        }
        /**
         * Returns the infoPlayer field.
         *
         * Uses a `info` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function infoPlayer(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->infoPlayer();
        }
        /**
         * Returns the headlinePodlove field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlinePodlove(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlinePodlove();
        }
        /**
         * Returns the podcasterPodloveActiveTab field.
         *
         * Uses a `select` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/select
         */
        public function podcasterPodloveActiveTab(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveActiveTab();
        }
        /**
         * Returns the podcasterPodloveColors field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterPodloveColors(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveColors();
        }
        /**
         * Returns the podcasterPodloveFonts field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterPodloveFonts(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveFonts();
        }
        /**
         * Returns the podcasterPodloveClients field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterPodloveClients(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveClients();
        }
        /**
         * Returns the podcasterPodloveShareChannels field.
         *
         * Uses a `multiselect` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/multiselect
         */
        public function podcasterPodloveShareChannels(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveShareChannels();
        }
        /**
         * Returns the podcasterPodloveSharePlaytime field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterPodloveSharePlaytime(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveSharePlaytime();
        }
        /**
         * Returns the podcasterPodloveRoles field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterPodloveRoles(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveRoles();
        }
        /**
         * Returns the podcasterPodloveGroups field.
         *
         * Uses a `structure` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function podcasterPodloveGroups(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterPodloveGroups();
        }
        /**
         * Returns the headlineMatomo field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlineMatomo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlineMatomo();
        }
        /**
         * Returns the infoMatomo field.
         *
         * Uses a `info` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function infoMatomo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->infoMatomo();
        }
        /**
         * Returns the podcasterMatomoEnabled field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoEnabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoEnabled();
        }
        /**
         * Returns the podcasterMatomoSiteId field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoSiteId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoSiteId();
        }
        /**
         * Returns the podcasterMatomoTrackGoal field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoTrackGoal(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoTrackGoal();
        }
        /**
         * Returns the podcasterMatomoGoalId field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoGoalId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoGoalId();
        }
        /**
         * Returns the podcasterMatomoTrackEvent field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoTrackEvent(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoTrackEvent();
        }
        /**
         * Returns the podcasterMatomoEventName field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoEventName(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoEventName();
        }
        /**
         * Returns the podcasterMatomoAction field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoAction(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoAction();
        }
        /**
         * Returns the podcasterMatomoFeedEnabled field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoFeedEnabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedEnabled();
        }
        /**
         * Returns the podcasterMatomoFeedSiteId field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoFeedSiteId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedSiteId();
        }
        /**
         * Returns the podcasterMatomoFeedTrackGoal field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoFeedTrackGoal(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedTrackGoal();
        }
        /**
         * Returns the podcasterMatomoFeedPage field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoFeedPage(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedPage();
        }
        /**
         * Returns the podcasterMatomoFeedGoalId field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoFeedGoalId(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedGoalId();
        }
        /**
         * Returns the podcasterMatomoFeedTrackEvent field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoFeedTrackEvent(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedTrackEvent();
        }
        /**
         * Returns the podcasterMatomoFeedEventName field.
         *
         * Uses a `text` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function podcasterMatomoFeedEventName(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedEventName();
        }
        /**
         * Returns the podcasterMatomoFeedAction field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podcasterMatomoFeedAction(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podcasterMatomoFeedAction();
        }
        /**
         * Returns the headlinePodTrac field.
         *
         * Uses a `headline` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function headlinePodTrac(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->headlinePodTrac();
        }
        /**
         * Returns the podTracEnabled field.
         *
         * Uses a `toggle` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function podTracEnabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podTracEnabled();
        }
        /**
         * Returns the podTracUrl field.
         *
         * Uses a `url` field in the `pages/podcasterfeed` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/url
         */
        public function podTracUrl(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podTracUrl();
        }
        /**
         * Returns the info_note field.
         *
         * Uses a `info` field in the `pages/suche` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function info_note(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->info_note();
        }
        /**
         * Returns the reindex field.
         *
         * Uses a `tw-search-reindex` field in the `pages/suche` blueprint.
         */
        public function reindex(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->reindex();
        }
        /**
         * Returns the search_placeholder field.
         *
         * Uses a `text` field in the `pages/suche` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function search_placeholder(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->search_placeholder();
        }
        /**
         * Returns the search_dialog_title field.
         *
         * Uses a `text` field in the `pages/suche` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function search_dialog_title(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->search_dialog_title();
        }
        /**
         * Returns the search_results_limit field.
         *
         * Uses a `number` field in the `pages/suche` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/number
         */
        public function search_results_limit(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->search_results_limit();
        }
        /**
         * Returns the search_comments_enabled field.
         *
         * Uses a `toggle` field in the `pages/suche` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function search_comments_enabled(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->search_comments_enabled();
        }
        /**
         * Returns the members field.
         *
         * Uses a `structure` field in the `pages/team` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/structure
         */
        public function members(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->members();
        }
        /**
         * Returns the testimonial_text field.
         *
         * Uses a `textarea` field in the `pages/testimonial` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function testimonial_text(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->testimonial_text();
        }
        /**
         * Returns the photo field.
         *
         * Uses a `files` field in the `pages/testimonial` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/files
         */
        public function photo(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->photo();
        }
        /**
         * Returns the intro field.
         *
         * Uses a `textarea` field in the `pages/testimonials` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function intro(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->intro();
        }
        public function icGetMastodonUrl()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->icGetMastodonUrl();
        }
        public function icGetBlueskyUrl()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->icGetBlueskyUrl();
        }
        public function commentCount($language = null): int
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->commentCount($language);
        }
        public function commentsAreEnabled()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->commentsAreEnabled();
        }
        public function comments()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->comments();
        }
        public function atomLink()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->atomLink();
        }
        public function feedCover()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->feedCover();
        }
        public function podloveRoles()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podloveRoles();
        }
        public function podloveGroups()
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->podloveGroups();
        }
        public function participationHostCount(): int
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->participationHostCount();
        }
        public function participationGuestCount(): int
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->participationGuestCount();
        }
        public function participationTotalCount(): int
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->participationTotalCount();
        }
        public function episodeTypeLabel(): string
        {
            /** @var \Kirby\Cms\Page $instance */
            return $instance->episodeTypeLabel();
        }
    }
    class File
    {
        /**
         * Returns the episodeTitle field.
         *
         * Uses a `text` field in the `files/podcaster-episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function episodeTitle(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\File $instance */
            return $instance->episodeTitle();
        }
        /**
         * Returns the duration field.
         *
         * Uses a `text` field in the `files/podcaster-episode` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function duration(): \Kirby\Content\Field
        {
            /** @var \Kirby\Cms\File $instance */
            return $instance->duration();
        }
    }
    class Layout
    {
        /**
         * Returns the columns in this layout
         *
         * @return \Kirby\Cms\LayoutColumns|\Kirby\Cms\LayoutColumn[]
         */
        public function columns(): \Kirby\Cms\LayoutColumns
        {
            /** @var \Kirby\Cms\Layout $instance */
            return $instance->columns();
        }
    }
    class LayoutColumn
    {
        /**
         * Returns the blocks collection
         *
         * @param bool $includeHidden Sets whether to include hidden blocks
         * @return \Kirby\Cms\Blocks|\Kirby\Cms\Block[]
         */
        public function blocks(bool $includeHidden = false): \Kirby\Cms\Blocks
        {
            /** @var \Kirby\Cms\LayoutColumn $instance */
            return $instance->blocks($includeHidden);
        }
    }
}

namespace 
{
    class KontaktPage
    {
        /**
         * Returns the header field.
         *
         * Uses a `text` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function header(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->header();
        }
        /**
         * Returns the lead field.
         *
         * Uses a `textarea` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/textarea
         */
        public function lead(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->lead();
        }
        /**
         * Returns the blocks field.
         *
         * Uses a `blocks` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/blocks
         */
        public function blocks(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->blocks();
        }
        /**
         * Returns the form_fields field.
         *
         * Uses a `layout` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/layout
         */
        public function form_fields(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->form_fields();
        }
        /**
         * Returns the delivery_info field.
         *
         * Uses a `info` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/info
         */
        public function delivery_info(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->delivery_info();
        }
        /**
         * Returns the submit_headline field.
         *
         * Uses a `headline` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function submit_headline(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->submit_headline();
        }
        /**
         * Returns the label_submit field.
         *
         * Uses a `text` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/text
         */
        public function label_submit(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->label_submit();
        }
        /**
         * Returns the submit_gap field.
         *
         * Uses a `gap` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function submit_gap(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->submit_gap();
        }
        /**
         * Returns the success_headline field.
         *
         * Uses a `headline` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function success_headline(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->success_headline();
        }
        /**
         * Returns the success_type field.
         *
         * Uses a `toggles` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggles
         */
        public function success_type(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->success_type();
        }
        /**
         * Returns the success_text field.
         *
         * Uses a `writer` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/writer
         */
        public function success_text(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->success_text();
        }
        /**
         * Returns the success_page field.
         *
         * Uses a `pages` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/pages
         */
        public function success_page(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->success_page();
        }
        /**
         * Returns the success_gap field.
         *
         * Uses a `gap` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function success_gap(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->success_gap();
        }
        /**
         * Returns the error_headline field.
         *
         * Uses a `headline` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function error_headline(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->error_headline();
        }
        /**
         * Returns the error_invalidFields field.
         *
         * Uses a `writer` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/writer
         */
        public function error_invalidFields(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->error_invalidFields();
        }
        /**
         * Returns the error_gap field.
         *
         * Uses a `gap` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function error_gap(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->error_gap();
        }
        /**
         * Returns the save_headline field.
         *
         * Uses a `headline` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/headline
         */
        public function save_headline(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->save_headline();
        }
        /**
         * Returns the save_entries field.
         *
         * Uses a `toggle` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/toggle
         */
        public function save_entries(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->save_entries();
        }
        /**
         * Returns the save_gap field.
         *
         * Uses a `gap` field in the `pages/kontakt` blueprint.
         *
         * @see https://getkirby.com/docs/reference/panel/fields/gap
         */
        public function save_gap(): \Kirby\Content\Field
        {
            /** @var \KontaktPage $instance */
            return $instance->save_gap();
        }
        public function icGetMastodonUrl()
        {
            /** @var \KontaktPage $instance */
            return $instance->icGetMastodonUrl();
        }
        public function icGetBlueskyUrl()
        {
            /** @var \KontaktPage $instance */
            return $instance->icGetBlueskyUrl();
        }
        public function commentCount($language = null): int
        {
            /** @var \KontaktPage $instance */
            return $instance->commentCount($language);
        }
        public function commentsAreEnabled()
        {
            /** @var \KontaktPage $instance */
            return $instance->commentsAreEnabled();
        }
        public function comments()
        {
            /** @var \KontaktPage $instance */
            return $instance->comments();
        }
        public function atomLink()
        {
            /** @var \KontaktPage $instance */
            return $instance->atomLink();
        }
        public function feedCover()
        {
            /** @var \KontaktPage $instance */
            return $instance->feedCover();
        }
        public function podloveRoles()
        {
            /** @var \KontaktPage $instance */
            return $instance->podloveRoles();
        }
        public function podloveGroups()
        {
            /** @var \KontaktPage $instance */
            return $instance->podloveGroups();
        }
        public function participationHostCount(): int
        {
            /** @var \KontaktPage $instance */
            return $instance->participationHostCount();
        }
        public function participationGuestCount(): int
        {
            /** @var \KontaktPage $instance */
            return $instance->participationGuestCount();
        }
        public function participationTotalCount(): int
        {
            /** @var \KontaktPage $instance */
            return $instance->participationTotalCount();
        }
        public function episodeTypeLabel(): string
        {
            /** @var \KontaktPage $instance */
            return $instance->episodeTypeLabel();
        }
    }
    class FormEntryPage
    {
        public function icGetMastodonUrl()
        {
            /** @var \FormEntryPage $instance */
            return $instance->icGetMastodonUrl();
        }
        public function icGetBlueskyUrl()
        {
            /** @var \FormEntryPage $instance */
            return $instance->icGetBlueskyUrl();
        }
        public function commentCount($language = null): int
        {
            /** @var \FormEntryPage $instance */
            return $instance->commentCount($language);
        }
        public function commentsAreEnabled()
        {
            /** @var \FormEntryPage $instance */
            return $instance->commentsAreEnabled();
        }
        public function comments()
        {
            /** @var \FormEntryPage $instance */
            return $instance->comments();
        }
        public function atomLink()
        {
            /** @var \FormEntryPage $instance */
            return $instance->atomLink();
        }
        public function feedCover()
        {
            /** @var \FormEntryPage $instance */
            return $instance->feedCover();
        }
        public function podloveRoles()
        {
            /** @var \FormEntryPage $instance */
            return $instance->podloveRoles();
        }
        public function podloveGroups()
        {
            /** @var \FormEntryPage $instance */
            return $instance->podloveGroups();
        }
        public function participationHostCount(): int
        {
            /** @var \FormEntryPage $instance */
            return $instance->participationHostCount();
        }
        public function participationGuestCount(): int
        {
            /** @var \FormEntryPage $instance */
            return $instance->participationGuestCount();
        }
        public function participationTotalCount(): int
        {
            /** @var \FormEntryPage $instance */
            return $instance->participationTotalCount();
        }
        public function episodeTypeLabel(): string
        {
            /** @var \FormEntryPage $instance */
            return $instance->episodeTypeLabel();
        }
    }
}

namespace Kirby\Content
{
    class Field
    {
        /**
         * Converts the field value into a proper boolean and inverts it
         */
        public function isFalse(): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->isFalse();
        }
        /**
         * Converts the field value into a proper boolean
         */
        public function isTrue(): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->isTrue();
        }
        /**
         * Validates the field content with the given validator and parameters
         *
         * @param mixed ...$arguments A list of optional validator arguments
         */
        public function isValid(string $validator, ...$arguments): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->isValid($validator, ...$arguments);
        }
        /**
         * Converts a yaml or json field to a Blocks object
         *
         * @return \Kirby\Cms\Blocks|\Kirby\Cms\Block[]
         */
        public function toBlocks(): \Kirby\Cms\Blocks
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toBlocks();
        }
        /**
         * Converts the field value into a proper boolean
         *
         * @param bool $default Default value if the field is empty
         */
        public function toBool(bool $default = false): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toBool($default);
        }
        /**
         * Parses the field value with the given method
         *
         * @param string $method [',', 'yaml', 'json']
         */
        public function toData(string $method = ','): array
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toData($method);
        }
        /**
         * Converts the field value to a timestamp or a formatted date
         *
         * @param string|\IntlDateFormatter|null $format PHP date formatting string
         * @param string|null $fallback Fallback string for `strtotime`
         */
        public function toDate(\IntlDateFormatter|string|null $format = null, ?string $fallback = null): string|int|null
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toDate($format, $fallback);
        }
        /**
         * Parse yaml entries data and convert it to a
         * collection of field objects
         */
        public function toEntries(): \Kirby\Cms\Collection
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toEntries();
        }
        /**
         * Returns a file object from a filename in the field
         */
        public function toFile(): ?\Kirby\Cms\File
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toFile();
        }
        /**
         * Returns a file collection from a yaml list of filenames in the field
         *
         * @return \Kirby\Cms\Files|\Kirby\Cms\File[]
         */
        public function toFiles(string $separator = 'yaml'): \Kirby\Cms\Files
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toFiles($separator);
        }
        /**
         * Converts the field value into a proper float
         *
         * @param float $default Default value if the field is empty
         */
        public function toFloat(float $default = 0.0): float
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toFloat($default);
        }
        /**
         * Converts the field value into a proper integer
         *
         * @param int $default Default value if the field is empty
         */
        public function toInt(int $default): int
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toInt($default);
        }
        /**
         * Parse layouts and turn them into Layout objects
         *
         * @return \Kirby\Cms\Layouts|\Kirby\Cms\Layout[]
         */
        public function toLayouts(): \Kirby\Cms\Layouts
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toLayouts();
        }
        /**
         * Wraps a link tag around the field value. The field value is used as the link text
         *
         * @param mixed $attr1 Can be an optional Url. If no Url is set, the Url of the Page, File or Site will be used. Can also be an array of link attributes
         * @param mixed $attr2 If `$attr1` is used to set the Url, you can use `$attr2` to pass an array of additional attributes.
         */
        public function toLink(array|string|null $attr1 = null, ?array $attr2 = null): string
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toLink($attr1, $attr2);
        }
        /**
         * Parse yaml data and convert it to a
         * content object
         */
        public function toObject(): \Kirby\Content\Content
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toObject();
        }
        /**
         * Returns a page object from a page id in the field
         */
        public function toPage(): ?\Kirby\Cms\Page
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toPage();
        }
        /**
         * Returns a pages collection from a yaml list of page ids in the field
         *
         * @param string $separator Can be any other separator to split the field value by
         * @return \Kirby\Cms\Pages|\Kirby\Cms\Page[]
         */
        public function toPages(string $separator = 'yaml'): \Kirby\Cms\Pages
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toPages($separator);
        }
        /**
         * Turns the field value into an QR code object
         */
        public function toQrCode(): ?\Kirby\Image\QrCode
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toQrCode();
        }
        /**
         * Converts a yaml field to a Structure object
         *
         * @return \Kirby\Cms\Structure|\Kirby\Cms\StructureObject[]
         */
        public function toStructure(): \Kirby\Cms\Structure
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toStructure();
        }
        /**
         * Converts the field value to a Unix timestamp
         */
        public function toTimestamp(): int|false
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toTimestamp();
        }
        /**
         * Turns the field value into an absolute Url
         */
        public function toUrl(): ?string
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toUrl();
        }
        /**
         * Converts a user email address to a user object
         */
        public function toUser(): ?\Kirby\Cms\User
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toUser();
        }
        /**
         * Returns a users collection from a yaml list
         * of user email addresses in the field
         *
         * @return \Kirby\Cms\Users|\Kirby\Cms\User[]
         */
        public function toUsers(string $separator = 'yaml'): \Kirby\Cms\Users
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toUsers($separator);
        }
        /**
         * Returns the length of the field content
         */
        public function length(): int
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->length();
        }
        /**
         * Returns the number of words in the text
         */
        public function words(): int
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->words();
        }
        /**
         * Applies the callback function to the field
         *
         * @since 3.4.0
         */
        public function callback(\Closure $callback): mixed
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->callback($callback);
        }
        /**
         * Escapes the field value to be safely used in HTML
         * templates without the risk of XSS attacks
         *
         * @param string $context Location of output (`html`, `attr`, `js`, `css`, `url` or `xml`)
         */
        public function escape(string $context = 'html'): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->escape($context);
        }
        /**
         * Creates an excerpt of the field value without html
         * or any other formatting.
         */
        public function excerpt(int $chars, bool $strip = true, string $rep = ' …'): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->excerpt($chars, $strip, $rep);
        }
        /**
         * Converts the field content to valid HTML
         */
        public function html(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->html();
        }
        /**
         * Strips all block-level HTML elements from the field value,
         * it can be safely placed inside of other inline elements
         * without the risk of breaking the HTML structure.
         *
         * @since 3.3.0
         */
        public function inline(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->inline();
        }
        /**
         * Converts the field content from Markdown/Kirbytext to valid HTML
         */
        public function kirbytext(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->kirbytext($options);
        }
        /**
         * Converts the field content from inline Markdown/Kirbytext
         * to valid HTML
         *
         * @since 3.1.0
         */
        public function kirbytextinline(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->kirbytextinline($options);
        }
        /**
         * Parses all KirbyTags without also parsing Markdown
         */
        public function kirbytags(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->kirbytags();
        }
        /**
         * Converts the field content to lowercase
         */
        public function lower(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->lower();
        }
        /**
         * Converts markdown to valid HTML
         */
        public function markdown(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->markdown($options);
        }
        /**
         * Converts all line breaks in the field content to `<br>` tags.
         *
         * @since 3.3.0
         */
        public function nl2br(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->nl2br();
        }
        /**
         * Parses the field value as DOM and replaces
         * any permalinks in href/src attributes with
         * the regular url
         *
         * This method is still experimental! You can use
         * it to solve potential problems with permalinks
         * already, but it might change in the future.
         */
        public function permalinksToUrls(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->permalinksToUrls();
        }
        /**
         * Uses the field value as Kirby query
         */
        public function query(?string $expect = null): mixed
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->query($expect);
        }
        /**
         * It parses any queries found in the field value.
         *
         * @param string|null $fallback Fallback for tokens in the template that cannot be replaced (`null` to keep the original token)
         */
        public function replace(array $data = [], ?string $fallback = ''): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->replace($data, $fallback);
        }
        /**
         * Cuts the string after the given length and
         * adds "…" if it is longer
         *
         * @param int $length The number of characters in the string
         * @param string $appendix An optional replacement for the missing rest
         */
        public function short(int $length, string $appendix = '…'): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->short($length, $appendix);
        }
        /**
         * Converts the field content to a slug
         */
        public function slug(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->slug();
        }
        /**
         * Applies SmartyPants to the field
         */
        public function smartypants(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->smartypants();
        }
        /**
         * Splits the field content into an array
         */
        public function split($separator = ','): array
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->split($separator);
        }
        /**
         * Converts the field content to uppercase
         */
        public function upper(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->upper();
        }
        /**
         * Avoids typographical widows in strings by replacing
         * the last space with `&nbsp;`
         */
        public function widont(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->widont();
        }
        /**
         * Converts the field content to valid XML
         */
        public function xml(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->xml();
        }
        /**
         * Parses yaml in the field content and returns an array
         */
        public function yaml(): array
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->yaml();
        }
        /**
         * Converts the field value into a proper boolean
         *
         * @param bool $default Default value if the field is empty
         */
        public function bool(bool $default = false): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toBool($default);
        }
        /**
         * Escapes the field value to be safely used in HTML
         * templates without the risk of XSS attacks
         *
         * @param string $context Location of output (`html`, `attr`, `js`, `css`, `url` or `xml`)
         */
        public function esc(string $context = 'html'): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->escape($context);
        }
        /**
         * Converts the field value into a proper float
         *
         * @param float $default Default value if the field is empty
         */
        public function float(float $default = 0.0): float
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toFloat($default);
        }
        /**
         * Converts the field content to valid HTML
         */
        public function h(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->html();
        }
        /**
         * Converts the field value into a proper integer
         *
         * @param int $default Default value if the field is empty
         */
        public function int(int $default): int
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toInt($default);
        }
        /**
         * Converts the field content from Markdown/Kirbytext to valid HTML
         */
        public function kt(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->kirbytext($options);
        }
        /**
         * Converts the field content from inline Markdown/Kirbytext
         * to valid HTML
         *
         * @since 3.1.0
         */
        public function kti(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->kirbytextinline($options);
        }
        /**
         * Wraps a link tag around the field value. The field value is used as the link text
         *
         * @param mixed $attr1 Can be an optional Url. If no Url is set, the Url of the Page, File or Site will be used. Can also be an array of link attributes
         * @param mixed $attr2 If `$attr1` is used to set the Url, you can use `$attr2` to pass an array of additional attributes.
         */
        public function link(array|string|null $attr1 = null, ?array $attr2 = null): string
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->toLink($attr1, $attr2);
        }
        /**
         * Converts markdown to valid HTML
         */
        public function md(array $options = []): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->markdown($options);
        }
        /**
         * Applies SmartyPants to the field
         */
        public function sp(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->smartypants();
        }
        /**
         * Validates the field content with the given validator and parameters
         *
         * @param mixed ...$arguments A list of optional validator arguments
         */
        public function v(string $validator, ...$arguments): bool
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->isValid($validator, ...$arguments);
        }
        /**
         * Converts the field content to valid XML
         */
        public function x(): \Kirby\Content\Field
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->xml();
        }
        public function emoticons()
        {
            /** @var \Kirby\Content\Field $instance */
            return $instance->emoticons();
        }
    }
    class Content
    {
        /**
         * Returns all registered field objects
         *
         * @return \Kirby\Content\Field[]
         */
        public function fields(): array
        {
            /** @var \Kirby\Content\Content $instance */
            return $instance->fields();
        }
    }
}

namespace Medienbaecker\HelpView
{
    class HelpPage
    {
        public function icGetMastodonUrl()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->icGetMastodonUrl();
        }
        public function icGetBlueskyUrl()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->icGetBlueskyUrl();
        }
        public function commentCount($language = null): int
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->commentCount($language);
        }
        public function commentsAreEnabled()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->commentsAreEnabled();
        }
        public function comments()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->comments();
        }
        public function atomLink()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->atomLink();
        }
        public function feedCover()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->feedCover();
        }
        public function podloveRoles()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->podloveRoles();
        }
        public function podloveGroups()
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->podloveGroups();
        }
        public function participationHostCount(): int
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->participationHostCount();
        }
        public function participationGuestCount(): int
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->participationGuestCount();
        }
        public function participationTotalCount(): int
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->participationTotalCount();
        }
        public function episodeTypeLabel(): string
        {
            /** @var \Medienbaecker\HelpView\HelpPage $instance */
            return $instance->episodeTypeLabel();
        }
    }
}

namespace Kirby\Toolkit
{
    class V
    {
        /**
         * Valid: `'yes' | true | 1 | 'on'`
         */
        public static function accepted($value): bool
        {
            return V::accepted($value);
        }
        /**
         * Valid: `a-z | A-Z`
         */
        public static function alpha($value, bool $unicode = false): bool
        {
            return V::alpha($value, $unicode);
        }
        /**
         * Valid: `a-z | A-Z | 0-9`
         */
        public static function alphanum($value, bool $unicode = false): bool
        {
            return V::alphanum($value, $unicode);
        }
        /**
         * Checks for numbers within the given range
         */
        public static function between($value, $min, $max): bool
        {
            return V::between($value, $min, $max);
        }
        /**
         * Checks with the callback sent by the user
         * It's ideal for one-time custom validations
         */
        public static function callback($value, callable $callback): bool
        {
            return V::callback($value, $callback);
        }
        /**
         * Checks if the given string contains the given value
         */
        public static function contains($value, $needle): bool
        {
            return V::contains($value, $needle);
        }
        /**
         * Checks for a valid date or compares two
         * dates with each other.
         *
         * Pass only the first argument to check for a valid date.
         * Pass an operator as second argument and another date as
         * third argument to compare them.
         */
        public static function date(?string $value, ?string $operator = null, ?string $test = null): bool
        {
            return V::date($value, $operator, $test);
        }
        /**
         * Valid: `'no' | false | 0 | 'off'`
         */
        public static function denied($value): bool
        {
            return V::denied($value);
        }
        /**
         * Checks for a value, which does not equal the given value
         */
        public static function different($value, $other, $strict = false): bool
        {
            return V::different($value, $other, $strict);
        }
        /**
         * Checks for valid email addresses
         */
        public static function email($value): bool
        {
            return V::email($value);
        }
        /**
         * Checks for empty values
         */
        public static function empty($value = null): bool
        {
            return V::empty($value);
        }
        /**
         * Checks if the given string ends with the given value
         */
        public static function endsWith(string $value, string $end): bool
        {
            return V::endsWith($value, $end);
        }
        /**
         * Checks for a valid filename
         */
        public static function filename($value): bool
        {
            return V::filename($value);
        }
        /**
         * Checks if the value exists in a list of given values
         */
        public static function in($value, array $in, bool $strict = false): bool
        {
            return V::in($value, $in, $strict);
        }
        /**
         * Checks for a valid integer
         */
        public static function integer($value, bool $strict = false): bool
        {
            return V::integer($value, $strict);
        }
        /**
         * Checks for a valid IP address
         */
        public static function ip($value): bool
        {
            return V::ip($value);
        }
        /**
         * Checks for valid json
         *
         * @psalm-suppress UnusedFunctionCall
         */
        public static function json($value): bool
        {
            return V::json($value);
        }
        /**
         * Checks if the value is lower than the second value
         */
        public static function less($value, float $max): bool
        {
            return V::less($value, $max);
        }
        /**
         * Checks if the value matches the given regular expression
         */
        public static function match($value, string $pattern): bool
        {
            return V::match($value, $pattern);
        }
        /**
         * Checks if the value does not exceed the maximum value
         */
        public static function max($value, float $max): bool
        {
            return V::max($value, $max);
        }
        /**
         * Checks if the value is higher than the minimum value
         */
        public static function min($value, float $min): bool
        {
            return V::min($value, $min);
        }
        /**
         * Checks if the number of characters in the value equals or is below the given maximum
         */
        public static function maxLength(?string $value, $max): bool
        {
            return V::maxLength($value, $max);
        }
        /**
         * Checks if the number of characters in the value equals or is greater than the given minimum
         */
        public static function minLength(?string $value, $min): bool
        {
            return V::minLength($value, $min);
        }
        /**
         * Checks if the number of words in the value equals or is below the given maximum
         */
        public static function maxWords(?string $value, $max): bool
        {
            return V::maxWords($value, $max);
        }
        /**
         * Checks if the number of words in the value equals or is below the given maximum
         */
        public static function minWords(?string $value, $min): bool
        {
            return V::minWords($value, $min);
        }
        /**
         * Checks if the first value is higher than the second value
         */
        public static function more($value, float $min): bool
        {
            return V::more($value, $min);
        }
        /**
         * Checks that the given string does not contain the second value
         */
        public static function notContains($value, $needle): bool
        {
            return V::notContains($value, $needle);
        }
        /**
         * Checks that the given value is not empty
         */
        public static function notEmpty($value): bool
        {
            return V::notEmpty($value);
        }
        /**
         * Checks that the given value is not in the given list of values
         */
        public static function notIn($value, $notIn): bool
        {
            return V::notIn($value, $notIn);
        }
        /**
         * Checks for a valid number / numeric value (float, int, double)
         */
        public static function num($value): bool
        {
            return V::num($value);
        }
        /**
         * Checks if the value is present
         */
        public static function required($value, $array = null): bool
        {
            return V::required($value, $array);
        }
        /**
         * Checks that the first value equals the second value
         */
        public static function same($value, $other, bool $strict = false): bool
        {
            return V::same($value, $other, $strict);
        }
        /**
         * Checks that the value has the given size
         */
        public static function size($value, $size, $operator = '=='): bool
        {
            return V::size($value, $size, $operator);
        }
        /**
         * Checks that the string starts with the given start value
         */
        public static function startsWith(string $value, string $start): bool
        {
            return V::startsWith($value, $start);
        }
        /**
         * Checks for a valid unformatted telephone number
         */
        public static function tel($value): bool
        {
            return V::tel($value);
        }
        /**
         * Checks for valid time
         */
        public static function time($value): bool
        {
            return V::time($value);
        }
        /**
         * Checks for a valid Url
         */
        public static function url($value): bool
        {
            return V::url($value);
        }
        /**
         * Checks for a valid Uuid, optionally for specific model type
         */
        public static function uuid(string $value, array|string|null $type = null): bool
        {
            return V::uuid($value, $type);
        }
        public static function file($value)
        {
            return V::file($value);
        }
        public static function requiredFile($value)
        {
            return V::requiredFile($value);
        }
        public static function filesize($value, $size)
        {
            return V::filesize($value, $size);
        }
        public static function mime($value, $allowed)
        {
            return V::mime($value, $allowed);
        }
        public static function image($value)
        {
            return V::image($value);
        }
    }
}

namespace 
{
    class Collection extends \Kirby\Cms\Collection {}
    class File extends \Kirby\Cms\File {}
    class Files extends \Kirby\Cms\Files {}
    class Find extends \Kirby\Cms\Find {}
    class Helpers extends \Kirby\Cms\Helpers {}
    class Html extends \Kirby\Cms\Html {}
    class kirby extends \Kirby\Cms\App {}
    class Page extends \Kirby\Cms\Page {}
    class Pages extends \Kirby\Cms\Pages {}
    class Pagination extends \Kirby\Cms\Pagination {}
    class R extends \Kirby\Cms\R {}
    class Response extends \Kirby\Cms\Response {}
    class S extends \Kirby\Cms\S {}
    class Sane extends \Kirby\Sane\Sane {}
    class Site extends \Kirby\Cms\Site {}
    class Structure extends \Kirby\Cms\Structure {}
    class Url extends \Kirby\Cms\Url {}
    class User extends \Kirby\Cms\User {}
    class Users extends \Kirby\Cms\Users {}
    class Visitor extends \Kirby\Cms\Visitor {}
    class Field extends \Kirby\Content\Field {}
    class Data extends \Kirby\Data\Data {}
    class Json extends \Kirby\Data\Json {}
    class Yaml extends \Kirby\Data\Yaml {}
    class Asset extends \Kirby\Filesystem\Asset {}
    class Dir extends \Kirby\Filesystem\Dir {}
    class F extends \Kirby\Filesystem\F {}
    class Mime extends \Kirby\Filesystem\Mime {}
    class Database extends \Kirby\Database\Database {}
    class Db extends \Kirby\Database\Db {}
    class ErrorPageException extends \Kirby\Exception\ErrorPageException {}
    class Cookie extends \Kirby\Http\Cookie {}
    class Header extends \Kirby\Http\Header {}
    class Remote extends \Kirby\Http\Remote {}
    class Dimensions extends \Kirby\Image\Dimensions {}
    class Panel extends \Kirby\Panel\Panel {}
    class Snippet extends \Kirby\Template\Snippet {}
    class Slot extends \Kirby\Template\Slot {}
    class A extends \Kirby\Toolkit\A {}
    class c extends \Kirby\Toolkit\Config {}
    class Config extends \Kirby\Toolkit\Config {}
    class Escape extends \Kirby\Toolkit\Escape {}
    class I18n extends \Kirby\Toolkit\I18n {}
    class Obj extends \Kirby\Toolkit\Obj {}
    class Str extends \Kirby\Toolkit\Str {}
    class Tpl extends \Kirby\Toolkit\Tpl {}
    class V extends \Kirby\Toolkit\V {}
    class Xml extends \Kirby\Toolkit\Xml {}
}

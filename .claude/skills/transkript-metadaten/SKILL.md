---
name: transkript-metadaten
description: After a transcript was imported (skill transkript-import), make the episode's hosts/guests match the audio - add missing participants to "Team & Gastmoderation" / "Gäste", create minimal participant stubs, set Gastmoderation, and fill in the episode's topics (main topic, general topics from the team's topic catalog) when still empty - with the scripts in scripts/transcripts/ (meta.py, verify.py). Always run right after transkript-import for the same episode.
---

# Technikwürze metadata follow-up

`transkript-import` labels the speakers from the audio; this skill makes the **episode page metadata** agree with what
was heard. Run it immediately after the import, for the same episode, before moving on. Use the versioned scripts in
`scripts/transcripts/` (README there); never write ad-hoc helper scripts.

Compare the transcript (speaker labels from `verify.py`, self-introductions in the first minute, the outro) with the
page's `Team & Gastmoderation` (`Podcasterhosts`) and `Gäste` (`Podcasterguests`) and fix the differences.

## Role model

- **Team** = permanent team members (participant role `host`) → field `Team & Gastmoderation`.
- **Gast** = everyone else (role `guest`) → field `Gäste`.
- **Gastmoderation** = a guest who (co-)moderated the episode: participant page gets the extra role "Gastmoderation"
  (`meta.py guestmod "Name"`), and the person goes into `Team & Gastmoderation`, not into `Gäste`. The episode page
  always labels the first group "Moderation". When unsure whether someone is a guest moderator, ask David.
- Participants are pages in `content/3_teilnehmende`. Aliases: `Marcel Otten` = `Marcel Böttcher`; the audio spelling of
  names is often wrong (check `meta.py list` and the shownotes before treating a name as a new person).
- Listeners whose voice mail or comment is played, and audience members of live recordings, are **not** participants.

## Scope: add only, never remove

Missing or under-credited people are added automatically. Never remove someone from a field and never switch someone
between host/guest on your own: if the audio suggests a listed person was not involved, leave the data, note it
in your report and ask David. Remove only on David's explicit decision (`meta.py remove N H|G "Name"`). If a name or role is ambiguous (two plausible matches, unclear surname, unclear
host-vs-guest) do not guess — leave it open and report it.

## Pipeline

All commands from the repository root; `N` = episode number.

1. **Collect the people.** `python3 scripts/transcripts/verify.py N` lists labels that are not participants and
   participants without segments. Add what the intro/outro says ("heute mit …").
2. **Resolve to participant pages.** `python3 scripts/transcripts/meta.py list` prints uuid and title of all participants.
   Match first/last name allowing spelling variants.
3. **Create a stub if there is no page** (name clearly audible or in the shownotes):

   ```
   python3 scripts/transcripts/meta.py stub "First" "Last" first-last TW<N>
   ```

   Creates `content/3_teilnehmende/<slug>/participant.txt` **without** numeric prefix (unlisted, stays out of the public
   list), role `guest`, profile fields empty, Description marked `⚠ TODO: Profil vervollständigen`. Never invent profession, bio or
   links. Slug = lowercase `first-last` with umlauts transliterated (ä→ae, ö→oe, ü→ue, ß→ss), no collisions.
4. **Gastmoderation only if guest moderated:** `python3 scripts/transcripts/meta.py guestmod "Name"`.
5. **Add to the episode** (appends, no duplicates, keeps the list style of the file):

   ```
   python3 scripts/transcripts/meta.py add N H "Name" ["Name" ...]     # Team & Gastmoderation
   python3 scripts/transcripts/meta.py add N G "Name" ["Name" ...]     # Gäste
   ```

6. **Topics, only if still empty** (never overwrite what the team already set). Read the shownotes and the transcript and
   look at what the episode is actually about:

   ```
   python3 scripts/transcripts/meta.py topics N                         # current values + catalog
   python3 scripts/transcripts/meta.py settopics N main "Topic" [...]   # main topic (field "Hauptthema", 1-3 values)
   python3 scripts/transcripts/meta.py settopics N general "Topic" ...  # general topics, from the catalog only
   ```

   - **Main topic** (`Topics`): main topic first, not too granular (not „CSS“ when it is about a CMS); a mix of topics
     becomes a generic one (e.g. „News“); for magazine episodes with a guest the guest's topic comes first. If the main
     topic is unclear, leave the field empty and ask David.
   - **General topics** (`General-topics`): choose **only from the catalog** that the team maintains in the Panel
     (Site → Settings → „Themenkatalog“, `meta.py catalog`). `settopics … general` refuses values that are not in the
     catalog. Pick the few topics that really take up time in the episode (typically 1 to 4), not every term that is
     mentioned. Never add catalog entries yourself; if an important topic is missing, mention it in the report so Stefan
     can extend the catalog.
7. **Re-run `verify.py N`:** participants and speaker labels should now agree; only listener/audience warnings remain.

Mention in your report: created stubs, additions, the topics you set (and which are still open), open questions. Do not commit; the content repo is committed only on
request.

## Hard stop-rules

- Never remove people or change `participant_role` (host ↔ guest); only add, plus the Gastmoderation toggle.
- Never publish an episode, rename folders or touch unrelated fields (title, description, audio, dates, other blocks). Topics are the only content fields besides hosts/guests that this skill sets, and only while they are empty.
- Never invent a profession, bio or external profile for a stub.
- Check for a Panel draft (`content/.../_changes/episode.txt`) before editing an episode; `imp.py` prints `CHANGES`.
  Editing `episode.txt` does not touch the draft — report it, do not delete or merge drafts unasked.

## Optional: kirby MCP

If the `kirby` MCP server is connected, `kirby_read_page_content` / `kirby_update_page_content` can write the
`podcasterHosts` / `podcasterGuests` fields (`page://<uuid>` lists); creating a participant page always needs file
creation (`meta.py stub`), because there is no MCP tool for it.

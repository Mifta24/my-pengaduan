\# AGENTS.md Best practices



* Keep it short

Aim for ≤ 150 lines. Long files slow the agent and bury signal.



* Use concrete commands

Wrap commands in back-ticks so agents can copy-paste without guessing.



* Update alongside code

Treat AGENTS.md like code—PR reviewers should nudge updates when build steps change.



* One source of truth

Avoid duplicate docs; link to READMEs or design docs instead of pasting them.



* Make requests precise

The more precise your guidance for the task at hand, the more likely the agent is to accomplish that task to your liking.



* Verify before merging

Require objective proof: tests, lint, type check, and a diff confined to agreed paths.

